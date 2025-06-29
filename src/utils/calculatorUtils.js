// Utility functions for Team Efficiency Calculator

/**
 * Currency formatting helper
 * @param {number} amount - The amount to format
 * @param {string} currency - Currency code (AED, USD, etc.)
 * @param {object} currencies - Currency configuration object
 * @returns {string} Formatted currency string
 */
export const formatCurrency = (amount, currency, currencies) => {
  if (!amount || !currency || !currencies[currency]) return '0';
  
  const currencyData = currencies[currency];
  const formattedAmount = Math.round(amount).toLocaleString();
  
  if (currencyData.formatting === 'prefix') {
    return `${currencyData.symbol}${formattedAmount}`;
  } else {
    return `${currencyData.symbol} ${formattedAmount}`;
  }
};

/**
 * Validate company email (not personal email domains)
 * @param {string} email - Email to validate
 * @returns {boolean} True if valid company email
 */
export const isCompanyEmail = (email) => {
  if (!email || !email.trim() || !email.includes('@')) return false;
  const gmailDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'live.com', 'aol.com'];
  const domain = email.split('@')[1]?.toLowerCase();
  return domain && !gmailDomains.includes(domain);
};

/**
 * Validate mobile number format
 * @param {string} mobile - Mobile number to validate
 * @returns {boolean} True if valid mobile number
 */
export const isValidMobileNumber = (mobile) => {
  if (!mobile || !mobile.trim()) return false;
  const cleanNumber = mobile.replace(/[\s\-\(\)]/g, '');
  const internationalPattern = /^\+\d{1,4}\d{6,}$/;
  const domesticPattern = /^\d{6,}$/;
  return internationalPattern.test(cleanNumber) || domesticPattern.test(cleanNumber);
};

/**
 * Calculate EOS Gratuity based on UAE Labor Law
 * @param {number} salary - Annual salary
 * @returns {number} Annual EOS gratuity provision
 */
export const calculateEOSGratuity = (salary) => {
  if (!salary || salary <= 0) return 0;
  // 21 days salary per year (UAE Labor Law)
  return Math.round((salary / 365) * 21);
};

/**
 * Calculate diagnostic inefficiency score
 * @param {object} diagnosticAnswers - User answers to diagnostic questions
 * @param {array} questions - Question configuration
 * @returns {object} Diagnostic results
 */
export const calculateDiagnosticScore = (diagnosticAnswers, questions) => {
  if (!questions || questions.length === 0) {
    return { inefficiencyPercent: 0, timeWasteHours: 0, keyIssues: [] };
  }

  let totalScore = 0;
  let totalWeight = 0;
  let totalTimeWaste = 0;
  const keyIssues = [];

  questions.forEach((question, index) => {
    const answerIndex = diagnosticAnswers[index];
    if (answerIndex !== undefined && question.options[answerIndex]) {
      const selectedOption = question.options[answerIndex];
      const weightedScore = (selectedOption.inefficiency / 100) * question.weight;
      totalScore += weightedScore;
      totalWeight += question.weight;
      totalTimeWaste += selectedOption.time_loss;

      if (selectedOption.inefficiency >= 50) {
        keyIssues.push({
          area: question.question.split('?')[0] || 'Process Issue',
          impact: selectedOption.inefficiency >= 80 ? 'High' : 'Moderate'
        });
      }
    }
  });

  const inefficiencyPercent = totalWeight > 0 ? (totalScore / totalWeight) * 0.20 : 0;
  const timeWasteHours = totalTimeWaste / 60;

  return {
    inefficiencyPercent: Math.round(inefficiencyPercent * 100) / 100,
    timeWasteHours: Math.round(timeWasteHours * 10) / 10,
    keyIssues: keyIssues.slice(0, 3)
  };
};

/**
 * Calculate true employee cost including all overheads
 * @param {object} formData - Form data with salary and overhead costs
 * @param {object} roleDefaults - Default values for the selected role
 * @param {string} currency - Selected currency
 * @returns {object} Cost breakdown
 */
export const calculateEmployeeCost = (formData, roleDefaults, currency) => {
  try {
    const fullSalary = parseInt(formData.fullSalary) || (roleDefaults ? roleDefaults.salaries[currency] : 0);
    const autoEosGratuity = calculateEOSGratuity(fullSalary);
    
    const defaults = roleDefaults ? roleDefaults.overheads[currency] : {};
    const visaCosts = formData.visaCostsCustom ? (parseInt(formData.visaCosts) || 0) : (defaults.visaCosts || 0);
    const insurance = formData.insuranceCustom ? (parseInt(formData.insurance) || 0) : (defaults.insurance || 0);
    const training = formData.trainingCustom ? (parseInt(formData.training) || 0) : (defaults.training || 0);
    const equipment = formData.equipmentCustom ? (parseInt(formData.equipment) || 0) : (defaults.equipment || 0);
    const officeSpace = formData.officeSpaceCustom ? (parseInt(formData.officeSpace) || 0) : (defaults.officeSpace || 0);
    const eosGratuity = formData.eosGratuityCustom ? (parseInt(formData.eosGratuity) || 0) : autoEosGratuity;
    const otherCosts = parseInt(formData.otherCosts) || 0;
    
    const totalOverheads = visaCosts + insurance + training + equipment + officeSpace + eosGratuity + otherCosts;
    const trueCost = fullSalary + totalOverheads;
    
    return {
      fullSalary: Math.max(0, fullSalary),
      totalOverheads: Math.max(0, Math.round(totalOverheads)),
      trueCost: Math.max(0, Math.round(trueCost)),
      autoEosGratuity: Math.max(0, autoEosGratuity)
    };
  } catch (error) {
    console.error('Error calculating employee cost:', error);
    return { fullSalary: 0, totalOverheads: 0, trueCost: 0, autoEosGratuity: 0 };
  }
};

/**
 * Validate form step completion
 * @param {number} step - Current step number
 * @param {object} formData - Form data
 * @param {string} selectedTeam - Selected team ID
 * @param {string} selectedRole - Selected role ID
 * @param {object} taskAllocations - Task allocation percentages
 * @param {array} teamQuestions - Questions for the selected team
 * @returns {boolean} True if step is valid
 */
export const validateStep = (step, formData, selectedTeam, selectedRole, taskAllocations, teamQuestions) => {
  switch (step) {
    case 0:
      return formData.fullName?.trim() && 
             formData.companyEmail?.trim() && 
             isCompanyEmail(formData.companyEmail) && 
             formData.companyName?.trim() && 
             formData.mobileNumber?.trim() && 
             isValidMobileNumber(formData.mobileNumber) && 
             formData.selectedCurrency;
    case 1:
      return selectedTeam && selectedTeam !== '';
    case 2:
      return selectedRole && selectedRole !== '' && formData.teamSize?.trim();
    case 3:
      return formData.fullSalary && parseInt(formData.fullSalary) > 0;
    case 4:
      const totalAllocation = Object.values(taskAllocations).reduce((sum, val) => sum + val, 0);
      return totalAllocation >= 98 && totalAllocation <= 102;
    case 5:
      const questions = teamQuestions[selectedTeam] || [];
      return questions.every((_, index) => formData.diagnosticAnswers[index] !== undefined);
    case 6:
      return formData.primaryGoal && formData.targetEfficiency && formData.timeline;
    default:
      return true;
  }
};

/**
 * Safe number parsing with fallback
 * @param {string|number} value - Value to parse
 * @param {number} fallback - Fallback value if parsing fails
 * @returns {number} Parsed number or fallback
 */
export const safeParseInt = (value, fallback = 0) => {
  const parsed = parseInt(value);
  return isNaN(parsed) ? fallback : Math.max(0, parsed);
};

/**
 * Clamp value between min and max
 * @param {number} value - Value to clamp
 * @param {number} min - Minimum value
 * @param {number} max - Maximum value
 * @returns {number} Clamped value
 */
export const clamp = (value, min, max) => {
  return Math.min(Math.max(value, min), max);
};