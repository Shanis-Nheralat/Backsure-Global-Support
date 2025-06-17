import React, { useState, useEffect } from 'react';
import { 
  ChevronRight, ChevronDown, ChevronUp, CheckCircle, AlertCircle, Download, 
  Users, DollarSign, Clock, TrendingUp, Info, Building, Mail, User, Target, 
  Settings, Phone, CreditCard, Shield, Headphones, Calculator, FileText, AlertTriangle,
  Edit3, Eye
} from 'lucide-react';

const TeamEfficiencyCalculator = () => {
  // Form state
  const [currentStep, setCurrentStep] = useState(0);
  const [selectedTeam, setSelectedTeam] = useState('');
  const [selectedRole, setSelectedRole] = useState('');
  const [taskAllocations, setTaskAllocations] = useState({});
  const [showResults, setShowResults] = useState(false);
  const [results, setResults] = useState(null);
  const [showCalculationBreakdown, setShowCalculationBreakdown] = useState(false);
  
  const [formData, setFormData] = useState({
    fullName: '',
    companyEmail: '',
    companyName: '',
    mobileNumber: '',
    teamSize: '',
    fullSalary: '',
    selectedRole: '',
    visaCosts: '',
    visaCostsCustom: false,
    insurance: '',
    insuranceCustom: false,
    training: '',
    trainingCustom: false,
    equipment: '',
    equipmentCustom: false,
    officeSpace: '',
    officeSpaceCustom: false,
    eosGratuity: '',
    eosGratuityCustom: false,
    otherCosts: '',
    teamMaturity: '',
    diagnosticAnswers: {},
    primaryGoal: '',
    targetEfficiency: '',
    timeline: ''
  });
  
  const totalSteps = 7;
  const progress = ((currentStep + 1) / totalSteps) * 100;
  
  // Role-based salary defaults (UAE market data)
  const roleDefaults = {
    junior: {
      name: 'Junior-Level (Admin, Support Staff)',
      fullSalary: 48000,
      visaCosts: 3750,
      insurance: 1899,
      training: 1500,
      equipment: 4000, // Reduced by 50%
      officeSpace: 12000,
      bsgRate: 0.75 // 75% of full salary
    },
    mid: {
      name: 'Mid-Level (Accountant, HR Executive)',
      fullSalary: 60000,
      visaCosts: 3500,
      insurance: 1899,
      training: 2000,
      equipment: 4000, // Reduced by 50%
      officeSpace: 12000,
      bsgRate: 0.84 // 84% of full salary
    },
    senior: {
      name: 'Senior-Level (Finance Manager, Operations Head)',
      fullSalary: 120000,
      visaCosts: 5000,
      insurance: 4000,
      training: 3000,
      equipment: 5000, // Reduced by 50%
      officeSpace: 15000,
      bsgRate: 0.80 // 80% of full salary
    }
  };
  
  // Team-specific diagnostic questions
  const teamQuestions = {
    sales: [
      {
        question: "How often do follow-ups get missed or delayed?",
        weight: 25,
        options: [
          { label: "Never - We have automated systems", inefficiency: 0, time_loss: 0 },
          { label: "Sometimes - Manual tracking", inefficiency: 50, time_loss: 45 },
          { label: "Often - No systematic approach", inefficiency: 100, time_loss: 90 }
        ]
      },
      {
        question: "Are proposals created from templates or from scratch?",
        weight: 20,
        options: [
          { label: "Always use templates", inefficiency: 0, time_loss: 0 },
          { label: "Somewhat templated", inefficiency: 50, time_loss: 30 },
          { label: "Created manually each time", inefficiency: 100, time_loss: 60 }
        ]
      },
      {
        question: "Is CRM data entry automated or manual?",
        weight: 20,
        options: [
          { label: "Fully automated", inefficiency: 0, time_loss: 0 },
          { label: "Mixed approach", inefficiency: 50, time_loss: 30 },
          { label: "Completely manual", inefficiency: 100, time_loss: 60 }
        ]
      },
      {
        question: "How often do meetings need rescheduling due to conflicts?",
        weight: 15,
        options: [
          { label: "Rarely - Good coordination", inefficiency: 0, time_loss: 0 },
          { label: "Sometimes - Manual scheduling", inefficiency: 50, time_loss: 15 },
          { label: "Often - Poor coordination", inefficiency: 100, time_loss: 30 }
        ]
      },
      {
        question: "Are client communications tracked systematically?",
        weight: 20,
        options: [
          { label: "Always tracked automatically", inefficiency: 0, time_loss: 0 },
          { label: "Mixed tracking", inefficiency: 50, time_loss: 30 },
          { label: "Not systematically tracked", inefficiency: 100, time_loss: 60 }
        ]
      }
    ],
    underwriting: [
      {
        question: "How often do risk assessments get delayed due to missing information?",
        weight: 25,
        options: [
          { label: "Never - Complete data upfront", inefficiency: 0, time_loss: 0 },
          { label: "Sometimes - Minor delays", inefficiency: 50, time_loss: 45 },
          { label: "Often - Major delays", inefficiency: 100, time_loss: 90 }
        ]
      },
      {
        question: "Is policy data input automated or manual?",
        weight: 20,
        options: [
          { label: "Fully automated systems", inefficiency: 0, time_loss: 0 },
          { label: "Partially automated", inefficiency: 50, time_loss: 30 },
          { label: "Completely manual entry", inefficiency: 100, time_loss: 60 }
        ]
      },
      {
        question: "How often do you reassess the same risks multiple times?",
        weight: 20,
        options: [
          { label: "Never - Clear guidelines", inefficiency: 0, time_loss: 0 },
          { label: "Sometimes - Unclear cases", inefficiency: 50, time_loss: 30 },
          { label: "Often - No clear standards", inefficiency: 100, time_loss: 60 }
        ]
      },
      {
        question: "Are policy notes and decisions clearly documented?",
        weight: 15,
        options: [
          { label: "Always - Standardized format", inefficiency: 0, time_loss: 0 },
          { label: "Usually - Some gaps", inefficiency: 50, time_loss: 15 },
          { label: "Poorly documented", inefficiency: 100, time_loss: 30 }
        ]
      },
      {
        question: "How smooth are handoffs between underwriting and other teams?",
        weight: 20,
        options: [
          { label: "Seamless - Clear process", inefficiency: 0, time_loss: 0 },
          { label: "Some friction", inefficiency: 50, time_loss: 30 },
          { label: "Frequent miscommunication", inefficiency: 100, time_loss: 60 }
        ]
      }
    ],
    claims: [
      {
        question: "How often do required documents go missing or get delayed?",
        weight: 25,
        options: [
          { label: "Never - Digital tracking", inefficiency: 0, time_loss: 0 },
          { label: "Sometimes - Manual tracking", inefficiency: 50, time_loss: 45 },
          { label: "Often - Poor documentation", inefficiency: 100, time_loss: 90 }
        ]
      },
      {
        question: "Are claim status updates automated or manual?",
        weight: 20,
        options: [
          { label: "Fully automated", inefficiency: 0, time_loss: 0 },
          { label: "Partially automated", inefficiency: 50, time_loss: 30 },
          { label: "Completely manual", inefficiency: 100, time_loss: 60 }
        ]
      },
      {
        question: "How often do customers call asking for claim updates?",
        weight: 15,
        options: [
          { label: "Rarely - Proactive updates", inefficiency: 0, time_loss: 0 },
          { label: "Sometimes", inefficiency: 50, time_loss: 20 },
          { label: "Very often - Poor communication", inefficiency: 100, time_loss: 40 }
        ]
      },
      {
        question: "Is document verification done manually or automated?",
        weight: 20,
        options: [
          { label: "Automated verification", inefficiency: 0, time_loss: 0 },
          { label: "Semi-automated", inefficiency: 50, time_loss: 30 },
          { label: "Completely manual", inefficiency: 100, time_loss: 60 }
        ]
      },
      {
        question: "How often do claims get delayed due to missing information?",
        weight: 20,
        options: [
          { label: "Rarely - Complete submissions", inefficiency: 0, time_loss: 0 },
          { label: "Sometimes", inefficiency: 50, time_loss: 30 },
          { label: "Frequently - Poor initial review", inefficiency: 100, time_loss: 60 }
        ]
      }
    ],
    customer_service: [
      {
        question: "How quickly do you respond to customer inquiries?",
        weight: 25,
        options: [
          { label: "Immediately - Automated responses", inefficiency: 0, time_loss: 0 },
          { label: "Within hours - Manual tracking", inefficiency: 50, time_loss: 45 },
          { label: "Days - No systematic approach", inefficiency: 100, time_loss: 90 }
        ]
      },
      {
        question: "Is complaint tracking automated or manual?",
        weight: 20,
        options: [
          { label: "Fully automated system", inefficiency: 0, time_loss: 0 },
          { label: "Partially tracked", inefficiency: 50, time_loss: 30 },
          { label: "Manual spreadsheets", inefficiency: 100, time_loss: 60 }
        ]
      },
      {
        question: "How are policy changes processed?",
        weight: 20,
        options: [
          { label: "Automated workflow", inefficiency: 0, time_loss: 0 },
          { label: "Semi-automated", inefficiency: 50, time_loss: 30 },
          { label: "Completely manual", inefficiency: 100, time_loss: 60 }
        ]
      },
      {
        question: "How often do customers repeat the same information to different agents?",
        weight: 15,
        options: [
          { label: "Never - Shared system", inefficiency: 0, time_loss: 0 },
          { label: "Sometimes", inefficiency: 50, time_loss: 15 },
          { label: "Often - No information sharing", inefficiency: 100, time_loss: 30 }
        ]
      },
      {
        question: "Are customer interactions logged systematically?",
        weight: 20,
        options: [
          { label: "Always - Automated logging", inefficiency: 0, time_loss: 0 },
          { label: "Usually - Manual notes", inefficiency: 50, time_loss: 30 },
          { label: "Inconsistent logging", inefficiency: 100, time_loss: 60 }
        ]
      }
    ],
    finance: [
      {
        question: "How often do account reconciliations require manual intervention?",
        weight: 25,
        options: [
          { label: "Never - Fully automated", inefficiency: 0, time_loss: 0 },
          { label: "Sometimes - Semi-automated", inefficiency: 50, time_loss: 45 },
          { label: "Often - Mostly manual", inefficiency: 100, time_loss: 90 }
        ]
      },
      {
        question: "Are financial reports generated automatically or manually?",
        weight: 20,
        options: [
          { label: "Fully automated", inefficiency: 0, time_loss: 0 },
          { label: "Partially automated", inefficiency: 50, time_loss: 30 },
          { label: "Manual compilation", inefficiency: 100, time_loss: 60 }
        ]
      },
      {
        question: "How often do payment follow-ups get missed?",
        weight: 20,
        options: [
          { label: "Never - Automated reminders", inefficiency: 0, time_loss: 0 },
          { label: "Sometimes - Manual tracking", inefficiency: 50, time_loss: 30 },
          { label: "Often - No systematic follow-up", inefficiency: 100, time_loss: 60 }
        ]
      },
      {
        question: "How frequently do invoice errors occur?",
        weight: 15,
        options: [
          { label: "Rarely - Automated validation", inefficiency: 0, time_loss: 0 },
          { label: "Sometimes", inefficiency: 50, time_loss: 15 },
          { label: "Often - Manual processes", inefficiency: 100, time_loss: 30 }
        ]
      },
      {
        question: "Is expense tracking automated or manual?",
        weight: 20,
        options: [
          { label: "Fully automated", inefficiency: 0, time_loss: 0 },
          { label: "Semi-automated", inefficiency: 50, time_loss: 30 },
          { label: "Manual spreadsheets", inefficiency: 100, time_loss: 60 }
        ]
      }
    ],
    compliance: [
      {
        question: "How much time is spent preparing for audits?",
        weight: 25,
        options: [
          { label: "Minimal - Always audit-ready", inefficiency: 0, time_loss: 0 },
          { label: "Moderate preparation needed", inefficiency: 50, time_loss: 45 },
          { label: "Extensive preparation required", inefficiency: 100, time_loss: 90 }
        ]
      },
      {
        question: "Are compliance logs maintained automatically?",
        weight: 20,
        options: [
          { label: "Fully automated logging", inefficiency: 0, time_loss: 0 },
          { label: "Partially automated", inefficiency: 50, time_loss: 30 },
          { label: "Manual record keeping", inefficiency: 100, time_loss: 60 }
        ]
      },
      {
        question: "How often do policy updates get missed or delayed?",
        weight: 20,
        options: [
          { label: "Never - Automated alerts", inefficiency: 0, time_loss: 0 },
          { label: "Sometimes - Manual monitoring", inefficiency: 50, time_loss: 30 },
          { label: "Often - Poor tracking", inefficiency: 100, time_loss: 60 }
        ]
      },
      {
        question: "Do you experience alert fatigue from compliance systems?",
        weight: 15,
        options: [
          { label: "No - Smart filtering", inefficiency: 0, time_loss: 0 },
          { label: "Sometimes - Too many alerts", inefficiency: 50, time_loss: 15 },
          { label: "Often - Information overload", inefficiency: 100, time_loss: 30 }
        ]
      },
      {
        question: "Is regulatory reporting automated or manual?",
        weight: 20,
        options: [
          { label: "Fully automated", inefficiency: 0, time_loss: 0 },
          { label: "Semi-automated", inefficiency: 50, time_loss: 30 },
          { label: "Manual compilation", inefficiency: 100, time_loss: 60 }
        ]
      }
    ]
  };
  
  // Team definitions
  const teams = [
    {
      id: 'sales',
      name: 'Sales Team',
      icon: <DollarSign className="h-6 w-6" />,
      description: 'Lead generation, client meetings, proposal creation, follow-ups',
      bsgServices: [
        'Lead follow-up scheduling and reminders',
        'Proposal formatting and documentation', 
        'CRM updates and basic reporting',
        'Meeting scheduling and coordination',
        'Admin communication and internal reminders'
      ],
      tasks: [
        { id: 'lead_generation', name: 'Lead Generation & Prospecting', min: 0, max: 100, default: 25 },
        { id: 'client_meetings', name: 'Client Meetings & Presentations', min: 0, max: 100, default: 30 },
        { id: 'proposal_creation', name: 'Proposal Creation & Documentation', min: 0, max: 100, default: 20 },
        { id: 'follow_up', name: 'Follow-up & Relationship Management', min: 0, max: 100, default: 15 },
        { id: 'admin_sales', name: 'Administrative Tasks', min: 0, max: 100, default: 10 }
      ]
    },
    {
      id: 'underwriting',
      name: 'Underwriting Team',
      icon: <Shield className="h-6 w-6" />,
      description: 'Risk assessment, policy evaluation, decision making, documentation',
      bsgServices: [
        'Document collection and organization',
        'Initial risk data compilation',
        'Policy template preparation',
        'Stakeholder communication coordination',
        'Administrative documentation'
      ],
      tasks: [
        { id: 'risk_assessment', name: 'Risk Assessment & Analysis', min: 0, max: 100, default: 35 },
        { id: 'policy_review', name: 'Policy Review & Evaluation', min: 0, max: 100, default: 25 },
        { id: 'documentation', name: 'Documentation & Reporting', min: 0, max: 100, default: 20 },
        { id: 'stakeholder_communication', name: 'Stakeholder Communication', min: 0, max: 100, default: 10 },
        { id: 'admin_underwriting', name: 'Administrative Tasks', min: 0, max: 100, default: 10 }
      ]
    },
    {
      id: 'claims',
      name: 'Claims Team',
      icon: <FileText className="h-6 w-6" />,
      description: 'Claims processing, investigation, settlement, customer communication',
      bsgServices: [
        'Initial claim documentation',
        'Customer communication coordination',
        'Document verification and organization',
        'Settlement paperwork preparation',
        'Follow-up scheduling and reminders'
      ],
      tasks: [
        { id: 'claim_processing', name: 'Claim Processing & Investigation', min: 0, max: 100, default: 40 },
        { id: 'customer_communication', name: 'Customer Communication', min: 0, max: 100, default: 20 },
        { id: 'documentation_claims', name: 'Documentation & Record Keeping', min: 0, max: 100, default: 15 },
        { id: 'settlement_processing', name: 'Settlement Processing', min: 0, max: 100, default: 15 },
        { id: 'admin_claims', name: 'Administrative Tasks', min: 0, max: 100, default: 10 }
      ]
    },
    {
      id: 'customer_service',
      name: 'Customer Service',
      icon: <Headphones className="h-6 w-6" />,
      description: 'Customer inquiries, policy changes, renewals, complaints handling',
      bsgServices: [
        'Initial customer inquiry handling',
        'Policy change documentation',
        'Renewal reminders and coordination',
        'Complaint tracking and follow-up',
        'Customer communication scheduling'
      ],
      tasks: [
        { id: 'customer_inquiries', name: 'Customer Inquiries & Support', min: 0, max: 100, default: 40 },
        { id: 'policy_changes', name: 'Policy Changes & Updates', min: 0, max: 100, default: 20 },
        { id: 'renewals', name: 'Policy Renewals', min: 0, max: 100, default: 15 },
        { id: 'complaint_handling', name: 'Complaint Handling', min: 0, max: 100, default: 15 },
        { id: 'admin_cs', name: 'Administrative Tasks', min: 0, max: 100, default: 10 }
      ]
    },
    {
      id: 'finance',
      name: 'Finance Team',
      icon: <Calculator className="h-6 w-6" />,
      description: 'Accounting, reporting, billing, financial analysis, compliance',
      bsgServices: [
        'Invoice processing and tracking',
        'Basic bookkeeping support',
        'Report preparation and formatting',
        'Payment follow-up coordination',
        'Financial data compilation'
      ],
      tasks: [
        { id: 'accounting', name: 'Accounting & Bookkeeping', min: 0, max: 100, default: 30 },
        { id: 'financial_reporting', name: 'Financial Reporting', min: 0, max: 100, default: 20 },
        { id: 'billing_collections', name: 'Billing & Collections', min: 0, max: 100, default: 15 },
        { id: 'financial_analysis', name: 'Financial Analysis', min: 0, max: 100, default: 15 },
        { id: 'regulatory_reporting', name: 'Regulatory Reporting', min: 0, max: 100, default: 20 }
      ]
    },
    {
      id: 'compliance',
      name: 'Compliance Team',
      icon: <Settings className="h-6 w-6" />,
      description: 'Regulatory reporting, risk monitoring, audit support, documentation',
      bsgServices: [
        'Regulatory document compilation',
        'Compliance tracking and monitoring',
        'Audit documentation preparation',
        'Policy update coordination',
        'Training material organization'
      ],
      tasks: [
        { id: 'regulatory_monitoring', name: 'Regulatory Monitoring', min: 0, max: 100, default: 25 },
        { id: 'compliance_reporting', name: 'Compliance Reporting', min: 0, max: 100, default: 25 },
        { id: 'audit_support', name: 'Audit Support', min: 0, max: 100, default: 20 },
        { id: 'policy_updates', name: 'Policy & Procedure Updates', min: 0, max: 100, default: 15 },
        { id: 'training_compliance', name: 'Training & Communication', min: 0, max: 100, default: 15 }
      ]
    }
  ];
  
  // Get current team object
  const currentTeam = teams.find(team => team.id === selectedTeam);
  const currentRoleDefaults = roleDefaults[selectedRole];
  
  // Email validation for company emails only
  const isCompanyEmail = (email) => {
    const gmailDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'live.com'];
    const domain = email.split('@')[1]?.toLowerCase();
    return domain && !gmailDomains.includes(domain);
  };
  
  // Calculate total employee cost
  const calculateEmployeeCost = () => {
    const fullSalary = parseInt(formData.fullSalary) || (currentRoleDefaults ? currentRoleDefaults.fullSalary : 0);
    
    // Auto-calculate EOS/Gratuity (21 days salary = Annual Salary ÷ 365 × 21)
    const autoEosGratuity = Math.round((fullSalary / 365) * 21);
    
    // Get cost values (use custom if enabled, otherwise use defaults)
    const visaCosts = formData.visaCostsCustom ? 
      (parseInt(formData.visaCosts) || 0) : 
      (currentRoleDefaults ? currentRoleDefaults.visaCosts : 0);
      
    const insurance = formData.insuranceCustom ? 
      (parseInt(formData.insurance) || 0) : 
      (currentRoleDefaults ? currentRoleDefaults.insurance : 0);
      
    const training = formData.trainingCustom ? 
      (parseInt(formData.training) || 0) : 
      (currentRoleDefaults ? currentRoleDefaults.training : 0);
      
    const equipment = formData.equipmentCustom ? 
      (parseInt(formData.equipment) || 0) : 
      (currentRoleDefaults ? currentRoleDefaults.equipment : 0);
      
    const officeSpace = formData.officeSpaceCustom ? 
      (parseInt(formData.officeSpace) || 0) : 
      (currentRoleDefaults ? currentRoleDefaults.officeSpace : 0);
      
    const eosGratuity = formData.eosGratuityCustom ? 
      (parseInt(formData.eosGratuity) || 0) : 
      autoEosGratuity;
    
    const otherCosts = parseInt(formData.otherCosts) || 0;
    
    const totalOverheads = visaCosts + insurance + training + equipment + officeSpace + eosGratuity + otherCosts;
    const trueCost = fullSalary + totalOverheads;
    
    return {
      fullSalary,
      totalOverheads: Math.round(totalOverheads),
      trueCost: Math.round(trueCost),
      autoEosGratuity,
      breakdown: {
        visaCosts,
        insurance,
        training,
        equipment,
        officeSpace,
        eosGratuity,
        otherCosts
      }
    };
  };
  
  // Handle form input changes
  const handleInputChange = (field, value) => {
    setFormData(prev => ({
      ...prev,
      [field]: value
    }));
  };
  
  // Handle role selection
  const handleRoleSelection = (roleId) => {
    setSelectedRole(roleId);
    const defaults = roleDefaults[roleId];
    if (defaults) {
      setFormData(prev => ({
        ...prev,
        selectedRole: roleId,
        fullSalary: defaults.fullSalary.toString(),
        // Reset to defaults when role changes
        visaCosts: defaults.visaCosts.toString(),
        visaCostsCustom: false,
        insurance: defaults.insurance.toString(),
        insuranceCustom: false,
        training: defaults.training.toString(),
        trainingCustom: false,
        equipment: defaults.equipment.toString(),
        equipmentCustom: false,
        officeSpace: defaults.officeSpace.toString(),
        officeSpaceCustom: false,
        eosGratuity: Math.round((defaults.fullSalary / 365) * 21).toString(),
        eosGratuityCustom: false,
        otherCosts: '0'
      }));
    }
  };
  
  // Handle task allocation changes
  const handleTaskAllocation = (taskId, value) => {
    setTaskAllocations(prev => ({
      ...prev,
      [taskId]: parseInt(value)
    }));
  };
  
  // Handle diagnostic answer changes
  const handleDiagnosticAnswer = (questionIndex, optionIndex) => {
    setFormData(prev => ({
      ...prev,
      diagnosticAnswers: {
        ...prev.diagnosticAnswers,
        [questionIndex]: optionIndex
      }
    }));
  };
  
  // Calculate diagnostic results
  const calculateDiagnosticResults = () => {
    if (!currentTeam || !teamQuestions[currentTeam.id]) {
      return { inefficiencyPercent: 0, timeWasteHours: 0, keyIssues: [] };
    }
    
    const questions = teamQuestions[currentTeam.id];
    let totalScore = 0;
    let totalWeight = 0;
    let totalTimeWaste = 0;
    const keyIssues = [];
    
    questions.forEach((question, index) => {
      const answerIndex = formData.diagnosticAnswers[index];
      if (answerIndex !== undefined) {
        const selectedOption = question.options[answerIndex];
        const weightedScore = (selectedOption.inefficiency / 100) * question.weight;
        totalScore += weightedScore;
        totalWeight += question.weight;
        totalTimeWaste += selectedOption.time_loss;
        
        // Track high impact issues
        if (selectedOption.inefficiency >= 50) {
          keyIssues.push({
            area: question.question.split('?')[0],
            impact: selectedOption.inefficiency >= 80 ? 'High' : 'Moderate'
          });
        }
      }
    });
    
    const maxInefficiency = 20; // 20% max as agreed
    const inefficiencyPercent = totalWeight > 0 ? (totalScore / totalWeight) * (maxInefficiency / 100) : 0;
    const timeWasteHours = totalTimeWaste / 60; // Convert minutes to hours
    
    return {
      inefficiencyPercent: Math.round(inefficiencyPercent * 100) / 100, // Round to 2 decimal places
      timeWasteHours: Math.round(timeWasteHours * 10) / 10, // Round to 1 decimal place
      keyIssues: keyIssues.slice(0, 3) // Top 3 issues
    };
  };
  
  // Calculate total task allocation percentage
  const totalAllocation = Object.values(taskAllocations).reduce((sum, val) => sum + val, 0);
  
  // Validation for current step
  const validateStep = (step) => {
    switch (step) {
      case 0: // Contact info
        return formData.fullName && 
               formData.companyEmail && 
               isCompanyEmail(formData.companyEmail) &&
               formData.companyName &&
               formData.mobileNumber;
      case 1: // Team selection
        return selectedTeam;
      case 2: // Role selection
        return selectedRole && formData.teamSize;
      case 3: // Cost structure
        return formData.fullSalary;
      case 4: // Task allocation
        return totalAllocation >= 95 && totalAllocation <= 105;
      case 5: // Team diagnostic
        const questions = teamQuestions[selectedTeam];
        if (!questions) return false;
        return questions.every((_, index) => formData.diagnosticAnswers[index] !== undefined);
      case 6: // Goals
        return formData.primaryGoal && formData.targetEfficiency && formData.timeline;
      default:
        return true;
    }
  };
  
  // Navigate between steps
  const nextStep = () => {
    if (validateStep(currentStep) && currentStep < totalSteps - 1) {
      setCurrentStep(currentStep + 1);
    }
  };
  
  const prevStep = () => {
    if (currentStep > 0) {
      setCurrentStep(currentStep - 1);
    }
  };
  
  // Initialize task allocations when team is selected
  useEffect(() => {
    if (currentTeam) {
      const initialAllocations = {};
      currentTeam.tasks.forEach(task => {
        initialAllocations[task.id] = task.default;
      });
      setTaskAllocations(initialAllocations);
    }
  }, [currentTeam]);
  
  // Calculate efficiency results with honest methodology
  const calculateEfficiency = () => {
    const teamSize = parseInt(formData.teamSize) || 1;
    const employeeCost = calculateEmployeeCost();
    const { fullSalary, trueCost } = employeeCost;
    const diagnosticResults = calculateDiagnosticResults();
    
    // Use diagnostic results for productivity analysis (but not cost inflation)
    let productivityLoss = diagnosticResults.inefficiencyPercent;
    
    // Apply team maturity modifier (slight adjustment)
    const teamMaturity = parseInt(formData.teamMaturity) || 2;
    const maturityModifier = (4 - teamMaturity) * 0.01; // Max 3% adjustment
    productivityLoss += maturityModifier;
    
    // Cap at 20% maximum as agreed
    productivityLoss = Math.min(productivityLoss, 0.20);
    
    // BSG pricing based on role
    const bsgRate = currentRoleDefaults ? currentRoleDefaults.bsgRate : 0.80;
    const bsgCostPerEmployee = fullSalary * bsgRate;
    
    // HONEST COST COMPARISON (no phantom costs)
    const currentTeamCost = teamSize * trueCost; // Real cost paid
    const bsgTotalCost = teamSize * bsgCostPerEmployee; // Real BSG cost
    
    // Real savings = Real current cost - Real BSG cost
    const realSavings = currentTeamCost - bsgTotalCost;
    const savingsPerEmployee = trueCost - bsgCostPerEmployee;
    
    // Efficiency improvement (BSG should match or exceed current efficiency)
    const currentEfficiency = 100 - (productivityLoss * 100);
    const bsgMinimumEfficiency = Math.max(currentEfficiency + 1, 96); // At least 1% better or 96% minimum
    const efficiencyGain = bsgMinimumEfficiency - currentEfficiency;
    
    // ROI based on real savings
    const roi = realSavings > 0 ? (realSavings / bsgTotalCost) * 100 : 0;
    const hoursReclaimed = diagnosticResults.timeWasteHours * teamSize;
    
    // Create ranges for display (±10% for implementation variables)
    const savingsRange = {
      min: Math.round(realSavings * 0.90),
      max: Math.round(realSavings * 1.10)
    };
    
    const roiRange = {
      min: Math.round(roi * 0.90),
      max: Math.round(roi * 1.10)
    };
    
    return {
      employeeCost,
      teamSize,
      diagnosticResults,
      currentSituation: {
        teamCost: currentTeamCost,
        trueCostPerEmployee: trueCost,
        productivityLoss: Math.round(productivityLoss * 100),
        currentEfficiency: Math.round(currentEfficiency)
      },
      withBSG: {
        bsgCostPerEmployee: Math.round(bsgCostPerEmployee),
        bsgTotalCost: Math.round(bsgTotalCost),
        bsgEfficiency: bsgMinimumEfficiency
      },
      results: {
        realSavings: Math.round(realSavings),
        savingsPerEmployee: Math.round(savingsPerEmployee),
        roi: Math.round(roi),
        hoursReclaimed: Math.round(hoursReclaimed * 10) / 10,
        efficiencyGain: Math.round(efficiencyGain),
        savingsRange,
        roiRange,
        isPositiveSavings: realSavings > 0
      }
    };
  };
  
  // Handle form submission
  const handleSubmit = () => {
    const calculatedResults = calculateEfficiency();
    setResults(calculatedResults);
    setShowResults(true);
  };
  
  // Tooltip component
  const Tooltip = ({ children, text }) => (
    <div className="relative group inline-block ml-1">
      <Info className="h-4 w-4 text-blue-500 cursor-help" />
      <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-10 w-64">
        {text}
        <div className="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-800"></div>
      </div>
    </div>
  );
  
  // Team selection card component
  const TeamCard = ({ team, isSelected, onClick }) => (
    <div
      className={`p-6 border-2 rounded-xl cursor-pointer transition-all duration-300 text-center ${
        isSelected 
          ? 'border-blue-500 bg-blue-50 transform scale-105' 
          : 'border-gray-200 hover:border-blue-300 hover:shadow-md'
      }`}
      onClick={onClick}
    >
      <div className="text-blue-600 mb-3 flex justify-center">{team.icon}</div>
      <h3 className="font-semibold text-gray-900 mb-2">{team.name}</h3>
      <p className="text-sm text-gray-600">{team.description}</p>
    </div>
  );
  
  // Role selection card component
  const RoleCard = ({ roleId, roleData, isSelected, onClick }) => (
    <div
      className={`p-6 border-2 rounded-xl cursor-pointer transition-all duration-300 ${
        isSelected 
          ? 'border-blue-500 bg-blue-50 transform scale-105' 
          : 'border-gray-200 hover:border-blue-300 hover:shadow-md'
      }`}
      onClick={onClick}
    >
      <h3 className="font-semibold text-gray-900 mb-2">{roleData.name}</h3>
      <div className="text-sm text-gray-600 space-y-1">
        <div>Salary: AED {roleData.fullSalary.toLocaleString()}</div>
        <div>Total Cost: AED {(roleData.fullSalary + roleData.visaCosts + roleData.insurance + roleData.training + roleData.equipment + roleData.officeSpace).toLocaleString()}</div>
      </div>
    </div>
  );
  
  // Task slider component
  const TaskSlider = ({ task, value, onChange }) => (
    <div className="mb-6">
      <div className="flex items-center justify-between mb-2">
        <label className="font-medium text-gray-700 flex items-center">
          {task.name}
          <Tooltip text={`Percentage of weekly work time spent on ${task.name.toLowerCase()}`} />
        </label>
        <span className="text-blue-600 font-semibold">{value}%</span>
      </div>
      <input
        type="range"
        min="0"
        max="100"
        value={value}
        onChange={(e) => onChange(task.id, e.target.value)}
        className="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
        style={{
          background: `linear-gradient(to right, #3b82f6 0%, #3b82f6 ${value}%, #e5e7eb ${value}%, #e5e7eb 100%)`
        }}
      />
      <div className="flex justify-between text-xs text-gray-500 mt-1">
        <span>0%</span>
        <span>100%</span>
      </div>
    </div>
  );
  
  if (showResults && results) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 p-4">
        <div className="max-w-7xl mx-auto">
          <div className="bg-white rounded-2xl shadow-2xl overflow-hidden">
            {/* Results Header */}
            <div className="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-8 text-center">
              <h1 className="text-3xl font-bold mb-2">Team Efficiency Analysis Results</h1>
              <p className="text-blue-100">
                Analysis for {formData.companyName}'s {currentTeam?.name} ({results.teamSize} people)
              </p>
            </div>
            
            {/* Important Disclaimer */}
            <div className="bg-yellow-50 border-l-4 border-yellow-400 p-4">
              <div className="flex items-start">
                <AlertTriangle className="h-5 w-5 text-yellow-400 mt-0.5 mr-3 flex-shrink-0" />
                <div>
                  <p className="text-sm text-yellow-800">
                    <strong>Important:</strong> These are system-calculated estimates based on your inputs and UAE market data. 
                    Actual BSG investment and savings will be provided by our executives after detailed team analysis and consultation.
                  </p>
                </div>
              </div>
            </div>

            {/* Executive Summary */}
            <div className="p-6 bg-gradient-to-r from-gray-50 to-blue-50 border-b">
              <div className="max-w-4xl mx-auto text-center">
                <h2 className="text-xl font-semibold text-gray-900 mb-2">Executive Summary</h2>
                {results.results.isPositiveSavings ? (
                  <p className="text-gray-700">
                    Your {currentTeam?.name} operates at <span className="font-semibold text-blue-600">{results.currentSituation.currentEfficiency}% efficiency</span> with 
                    a cost of <span className="font-semibold text-gray-800">AED {results.currentSituation.teamCost.toLocaleString()}</span> annually. 
                    BSG provides the same services at <span className="font-semibold text-green-600">96% efficiency</span> for 
                    <span className="font-semibold text-green-600"> AED {results.withBSG.bsgTotalCost.toLocaleString()}</span>, 
                    saving <span className="font-semibold text-green-600">AED {results.results.savingsRange.min.toLocaleString()} - {results.results.savingsRange.max.toLocaleString()}</span> 
                    ({results.results.roiRange.min}%-{results.results.roiRange.max}% ROI).
                  </p>
                ) : (
                  <p className="text-gray-700">
                    Your {currentTeam?.name} operates at <span className="font-semibold text-blue-600">{results.currentSituation.currentEfficiency}% efficiency</span>. 
                    While BSG provides <span className="font-semibold text-green-600">96% efficiency</span> and eliminates overhead management, 
                    the cost comparison shows <span className="font-semibold text-orange-600">similar investment levels</span> with 
                    <span className="font-semibold text-green-600">operational benefits</span>.
                  </p>
                )}
              </div>
            </div>
            
            <div className="p-8 space-y-12">
              {/* Section 1: Team Efficiency Diagnostic */}
              <div className="bg-white rounded-xl border border-gray-200 p-8">
                <div className="text-center mb-8">
                  <h2 className="text-2xl font-bold text-gray-900 mb-2">🔍 Team Efficiency Diagnostic Summary</h2>
                  <p className="text-gray-600">Current performance analysis of your {currentTeam?.name}</p>
                </div>
                
                {/* Dashboard Cards */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                  <div className="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 text-center">
                    <div className="text-red-600 mb-2">
                      <AlertCircle className="h-8 w-8 mx-auto" />
                    </div>
                    <div className="text-3xl font-bold text-red-600 mb-1">
                      {results.currentSituation.productivityLoss}%
                    </div>
                    <div className="text-sm text-red-700 font-medium">Inefficiency Score</div>
                    <div className="text-xs text-red-600 mt-1">Based on team diagnostic</div>
                  </div>
                  
                  <div className="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 text-center">
                    <div className="text-blue-600 mb-2">
                      <Clock className="h-8 w-8 mx-auto" />
                    </div>
                    <div className="text-3xl font-bold text-blue-600 mb-1">
                      {results.results.hoursReclaimed}hrs
                    </div>
                    <div className="text-sm text-blue-700 font-medium">Weekly Time Waste</div>
                    <div className="text-xs text-blue-600 mt-1">
                      {(results.results.hoursReclaimed / results.teamSize).toFixed(1)} hrs per employee
                    </div>
                  </div>
                  
                  <div className="bg-orange-50 border-l-4 border-orange-500 rounded-lg p-6 text-center">
                    <div className="text-orange-600 mb-2">
                      <User className="h-8 w-8 mx-auto" />
                    </div>
                    <div className="text-3xl font-bold text-orange-600 mb-1">
                      AED {results.currentSituation.trueCostPerEmployee.toLocaleString()}
                    </div>
                    <div className="text-sm text-orange-700 font-medium">Cost per Employee</div>
                    <div className="text-xs text-orange-600 mt-1">True employment cost</div>
                  </div>
                  
                  <div className="bg-purple-50 border-l-4 border-purple-500 rounded-lg p-6 text-center">
                    <div className="text-purple-600 mb-2">
                      <Users className="h-8 w-8 mx-auto" />
                    </div>
                    <div className="text-3xl font-bold text-purple-600 mb-1">
                      AED {results.currentSituation.teamCost.toLocaleString()}
                    </div>
                    <div className="text-sm text-purple-700 font-medium">Total Annual Cost</div>
                    <div className="text-xs text-purple-600 mt-1">Actual amount paid</div>
                  </div>
                </div>
                
                {/* Efficiency Breakdown */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  {/* Time Efficiency Donut */}
                  <div className="bg-gray-50 rounded-lg p-6">
                    <h3 className="text-lg font-semibold text-gray-900 mb-4 text-center">Time Efficiency Breakdown</h3>
                    <div className="relative w-48 h-48 mx-auto mb-4">
                      <svg className="w-48 h-48" viewBox="0 0 42 42">
                        <circle cx="21" cy="21" r="15.915" fill="transparent" stroke="#e5e7eb" strokeWidth="3"/>
                        <circle 
                          cx="21" cy="21" r="15.915" fill="transparent" 
                          stroke="#ef4444" strokeWidth="3"
                          strokeDasharray={`${results.currentSituation.productivityLoss} ${100 - results.currentSituation.productivityLoss}`}
                          strokeDashoffset="25"
                        />
                        <circle 
                          cx="21" cy="21" r="15.915" fill="transparent" 
                          stroke="#10b981" strokeWidth="3"
                          strokeDasharray={`${results.currentSituation.currentEfficiency} ${results.currentSituation.productivityLoss}`}
                          strokeDashoffset={`${25 + results.currentSituation.productivityLoss}`}
                        />
                      </svg>
                      <div className="absolute inset-0 flex items-center justify-center">
                        <div className="text-center">
                          <div className="text-2xl font-bold text-gray-900">{results.currentSituation.currentEfficiency}%</div>
                          <div className="text-sm text-gray-600">Productive</div>
                        </div>
                      </div>
                    </div>
                    <div className="flex justify-center space-x-6 text-sm">
                      <div className="flex items-center">
                        <div className="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span>Productive ({results.currentSituation.currentEfficiency}%)</span>
                      </div>
                      <div className="flex items-center">
                        <div className="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        <span>Wasted ({results.currentSituation.productivityLoss}%)</span>
                      </div>
                    </div>
                  </div>
                  
                  {/* Friction Points */}
                  <div className="bg-gray-50 rounded-lg p-6">
                    <h3 className="text-lg font-semibold text-gray-900 mb-4">Key Friction Points Identified</h3>
                    {results.diagnosticResults.keyIssues.length > 0 ? (
                      <div className="space-y-3">
                        {results.diagnosticResults.keyIssues.map((issue, index) => (
                          <div key={index} className="flex items-start p-3 bg-white rounded-lg border">
                            <span className={`inline-block w-3 h-3 rounded-full mr-3 mt-1 ${
                              issue.impact === 'High' ? 'bg-red-500' : 'bg-yellow-500'
                            }`}></span>
                            <div className="flex-1">
                              <div className="font-medium text-gray-900">{issue.area}</div>
                              <div className="text-sm text-gray-600">{issue.impact} Impact</div>
                            </div>
                          </div>
                        ))}
                      </div>
                    ) : (
                      <div className="text-center py-8">
                        <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                          <CheckCircle className="h-8 w-8 text-green-600" />
                        </div>
                        <h4 className="text-lg font-medium text-green-800 mb-2">Highly Efficient Team!</h4>
                        <p className="text-sm text-green-700">
                          Your team shows excellent efficiency. BSG can still help with administrative overhead and scalability.
                        </p>
                      </div>
                    )}
                  </div>
                </div>
                
                {/* Cost Breakdown */}
                <div className="mt-8 bg-gray-50 rounded-lg p-6">
                  <h3 className="text-lg font-semibold text-gray-900 mb-4">Annual Cost Breakdown</h3>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="bg-white rounded-lg p-4 text-center">
                      <div className="text-2xl font-bold text-blue-600">AED {results.employeeCost.fullSalary.toLocaleString()}</div>
                      <div className="text-sm text-gray-600">Full Salary</div>
                      <div className="text-xs text-gray-500">Per employee annual</div>
                    </div>
                    <div className="bg-white rounded-lg p-4 text-center">
                      <div className="text-2xl font-bold text-orange-600">AED {results.employeeCost.totalOverheads.toLocaleString()}</div>
                      <div className="text-sm text-gray-600">Overhead Costs</div>
                      <div className="text-xs text-gray-500">Visa, insurance, office, etc.</div>
                    </div>
                  </div>
                  <div className="mt-4 p-4 bg-blue-100 rounded-lg text-center">
                    <div className="text-2xl font-bold text-purple-600">AED {results.currentSituation.teamCost.toLocaleString()}</div>
                    <div className="text-sm text-gray-700">Total Team Cost</div>
                    <div className="text-xs text-gray-600">Actual annual expense ({results.teamSize} employees)</div>
                  </div>
                  
                  <div className="mt-4 p-3 bg-yellow-50 rounded-lg">
                    <div className="text-sm text-yellow-800">
                      <strong>Efficiency Note:</strong> Your team operates at {results.currentSituation.currentEfficiency}% efficiency, 
                      meaning {results.currentSituation.productivityLoss}% of working time is spent on inefficient activities 
                      (getting less value for the same cost paid).
                    </div>
                  </div>
                </div>
              </div>
              
              {/* Section 2: BSG Solution Summary */}
              <div className="bg-white rounded-xl border border-gray-200 p-8">
                <div className="text-center mb-8">
                  <h2 className="text-2xl font-bold text-gray-900 mb-2">🎯 BSG Solution & Cost Efficiency Summary</h2>
                  <p className="text-gray-600">How BSG optimizes your {currentTeam?.name} performance</p>
                </div>
                
                {/* BSG Dashboard Cards */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                  <div className="bg-green-50 border-l-4 border-green-500 rounded-lg p-6 text-center">
                    <div className="text-green-600 mb-2">
                      <TrendingUp className="h-8 w-8 mx-auto" />
                    </div>
                    <div className="text-3xl font-bold text-green-600 mb-1">
                      {results.withBSG.bsgEfficiency}%
                    </div>
                    <div className="text-sm text-green-700 font-medium">BSG Efficiency</div>
                    <div className="text-xs text-green-600 mt-1">
                      {results.results.efficiencyGain > 0 ? `+${results.results.efficiencyGain}% improvement` : 'Maintains high efficiency'}
                    </div>
                  </div>
                  
                  <div className="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 text-center">
                    <div className="text-blue-600 mb-2">
                      <DollarSign className="h-8 w-8 mx-auto" />
                    </div>
                    <div className="text-3xl font-bold text-blue-600 mb-1">
                      {Math.round(currentRoleDefaults?.bsgRate * 100)}%
                    </div>
                    <div className="text-sm text-blue-700 font-medium">BSG Rate</div>
                    <div className="text-xs text-blue-600 mt-1">Of full salary</div>
                  </div>
                  
                  <div className="bg-orange-50 border-l-4 border-orange-500 rounded-lg p-6 text-center">
                    <div className="text-orange-600 mb-2">
                      <User className="h-8 w-8 mx-auto" />
                    </div>
                    <div className="text-3xl font-bold text-orange-600 mb-1">
                      AED {results.withBSG.bsgCostPerEmployee.toLocaleString()}
                    </div>
                    <div className="text-sm text-orange-700 font-medium">Per Employee Fee</div>
                    <div className="text-xs text-orange-600 mt-1">No overhead costs</div>
                  </div>
                  
                  <div className="bg-purple-50 border-l-4 border-purple-500 rounded-lg p-6 text-center">
                    <div className="text-purple-600 mb-2">
                      <Users className="h-8 w-8 mx-auto" />
                    </div>
                    <div className="text-3xl font-bold text-purple-600 mb-1">
                      AED {results.withBSG.bsgTotalCost.toLocaleString()}
                    </div>
                    <div className="text-sm text-purple-700 font-medium">Annual Investment</div>
                    <div className="text-xs text-purple-600 mt-1">Total team cost</div>
                  </div>
                </div>
                
                {/* BSG Solution Details */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  {/* BSG Cost Allocation */}
                  <div className="bg-gray-50 rounded-lg p-6">
                    <h3 className="text-lg font-semibold text-gray-900 mb-4 text-center">BSG Efficiency Structure</h3>
                    <div className="relative w-48 h-48 mx-auto mb-4">
                      <svg className="w-48 h-48" viewBox="0 0 42 42">
                        <circle cx="21" cy="21" r="15.915" fill="transparent" stroke="#e5e7eb" strokeWidth="3"/>
                        <circle 
                          cx="21" cy="21" r="15.915" fill="transparent" 
                          stroke="#3b82f6" strokeWidth="3"
                          strokeDasharray={`${results.withBSG.bsgEfficiency} ${100 - results.withBSG.bsgEfficiency}`}
                          strokeDashoffset="25"
                        />
                        <circle 
                          cx="21" cy="21" r="15.915" fill="transparent" 
                          stroke="#ef4444" strokeWidth="3"
                          strokeDasharray={`${100 - results.withBSG.bsgEfficiency} ${results.withBSG.bsgEfficiency}`}
                          strokeDashoffset={`${25 + results.withBSG.bsgEfficiency}`}
                        />
                      </svg>
                      <div className="absolute inset-0 flex items-center justify-center">
                        <div className="text-center">
                          <div className="text-2xl font-bold text-gray-900">{results.withBSG.bsgEfficiency}%</div>
                          <div className="text-sm text-gray-600">Efficient</div>
                        </div>
                      </div>
                    </div>
                    <div className="flex justify-center space-x-6 text-sm">
                      <div className="flex items-center">
                        <div className="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span>BSG Efficiency ({results.withBSG.bsgEfficiency}%)</span>
                      </div>
                      <div className="flex items-center">
                        <div className="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        <span>Minimal Loss ({100 - results.withBSG.bsgEfficiency}%)</span>
                      </div>
                    </div>
                  </div>
                  
                  {/* BSG Services */}
                  <div className="bg-gray-50 rounded-lg p-6">
                    <h3 className="text-lg font-semibold text-gray-900 mb-4">BSG Services Included</h3>
                    <div className="space-y-3">
                      {currentTeam?.bsgServices.map((service, idx) => (
                        <div key={idx} className="flex items-start p-3 bg-white rounded-lg border">
                          <CheckCircle className="h-5 w-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" />
                          <div className="text-sm text-gray-700">{service}</div>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
                
                {/* ROI & Savings */}
                <div className="mt-8 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-6">
                  <h3 className="text-lg font-semibold text-gray-900 mb-4 text-center">Cost Comparison & Business Impact</h3>
                  {results.results.isPositiveSavings ? (
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                      <div className="bg-white rounded-lg p-4 text-center">
                        <div className="text-2xl font-bold text-green-600">AED {results.results.savingsRange.min.toLocaleString()}</div>
                        <div className="text-sm text-gray-600">Minimum Savings</div>
                        <div className="text-xs text-gray-500">Conservative estimate</div>
                      </div>
                      <div className="bg-white rounded-lg p-4 text-center">
                        <div className="text-2xl font-bold text-green-600">AED {results.results.savingsRange.max.toLocaleString()}</div>
                        <div className="text-sm text-gray-600">Maximum Savings</div>
                        <div className="text-xs text-gray-500">Optimistic estimate</div>
                      </div>
                      <div className="bg-white rounded-lg p-4 text-center">
                        <div className="text-2xl font-bold text-purple-600">{results.results.roiRange.min}% - {results.results.roiRange.max}%</div>
                        <div className="text-sm text-gray-600">ROI Range</div>
                        <div className="text-xs text-gray-500">Return on investment</div>
                      </div>
                      <div className="bg-white rounded-lg p-4 text-center">
                        <div className="text-2xl font-bold text-blue-600">
                          {results.results.efficiencyGain > 0 ? `+${results.results.efficiencyGain}%` : 'Maintained'}
                        </div>
                        <div className="text-sm text-gray-600">Efficiency Gain</div>
                        <div className="text-xs text-gray-500">
                          {results.results.efficiencyGain > 0 ? 'Performance improvement' : 'High efficiency maintained'}
                        </div>
                      </div>
                    </div>
                  ) : (
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div className="bg-white rounded-lg p-4 text-center">
                        <div className="text-2xl font-bold text-orange-600">AED {Math.abs(results.results.realSavings).toLocaleString()}</div>
                        <div className="text-sm text-gray-600">Additional Investment</div>
                        <div className="text-xs text-gray-500">Cost difference</div>
                      </div>
                      <div className="bg-white rounded-lg p-4 text-center">
                        <div className="text-2xl font-bold text-green-600">
                          {results.results.efficiencyGain > 0 ? `+${results.results.efficiencyGain}%` : 'Maintained'}
                        </div>
                        <div className="text-sm text-gray-600">Efficiency Gain</div>
                        <div className="text-xs text-gray-500">
                          {results.results.efficiencyGain > 0 ? 'Performance improvement' : 'High efficiency maintained'}
                        </div>
                      </div>
                      <div className="bg-white rounded-lg p-4 text-center">
                        <div className="text-2xl font-bold text-blue-600">0%</div>
                        <div className="text-sm text-gray-600">Overhead Management</div>
                        <div className="text-xs text-gray-500">BSG handles all HR/Admin</div>
                      </div>
                    </div>
                  )}
                  
                  <div className="mt-4 p-3 bg-blue-100 rounded-lg text-center">
                    <div className="text-sm text-blue-800">
                      <strong>Key Value:</strong> BSG eliminates visa, office, HR management overhead while providing 
                      {results.results.hoursReclaimed} hours/week of reclaimed time and 
                      {results.results.efficiencyGain > 0 ? `${results.results.efficiencyGain}% efficiency improvement` : 'maintains your high efficiency standards'}.
                    </div>
                  </div>
                </div>
              </div>
              
              {/* CTA Section */}
              <div className="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-8 text-center">
                <h3 className="text-2xl font-bold text-gray-900 mb-4">
                  Ready to Transform Your {currentTeam?.name}?
                </h3>
                <p className="text-gray-600 mb-6">
                  BSG Support can help implement these efficiency improvements for your {currentTeam?.name.toLowerCase()}. 
                  Our executives will provide detailed analysis and exact investment requirements based on your specific needs.
                </p>
                <div className="flex flex-col sm:flex-row gap-4 justify-center">
                  <a
                    href={`mailto:info@bsgsupport.com?subject=${encodeURIComponent(currentTeam?.name + ' Efficiency Analysis Follow-up')}&body=${encodeURIComponent(`Hello,

I completed the team efficiency analysis for our ${currentTeam?.name} and would like to discuss the results.

Company: ${formData.companyName}
Contact: ${formData.fullName}
Email: ${formData.companyEmail}
Mobile: ${formData.mobileNumber}

Team Details:
- Team Type: ${currentTeam?.name}
- Team Size: ${results.teamSize} people
- Role Level: ${currentRoleDefaults?.name}

Results Summary:
- Current Annual Cost: AED ${results.currentSituation.teamCost.toLocaleString()}
- Current Efficiency: ${results.currentSituation.currentEfficiency}%
- BSG Annual Cost: AED ${results.withBSG.bsgTotalCost.toLocaleString()}
- BSG Efficiency: ${results.withBSG.bsgEfficiency}%
- Potential Savings: AED ${results.results.savingsRange.min.toLocaleString()} - ${results.results.savingsRange.max.toLocaleString()}
- Weekly Hours Reclaimed: ${results.results.hoursReclaimed}hrs
- Estimated ROI: ${results.results.roiRange.min}% - ${results.results.roiRange.max}%
- Efficiency Gain: ${results.results.efficiencyGain > 0 ? `+${results.results.efficiencyGain}%` : 'High efficiency maintained'}

Key Issues Identified:
${results.diagnosticResults.keyIssues.map(issue => `- ${issue.area} (${issue.impact} Impact)`).join('\n')}

BSG Services Needed:
${currentTeam?.bsgServices.map(service => `- ${service}`).join('\n')}

Please contact me to schedule a consultation for detailed analysis and exact investment requirements.

Best regards,
${formData.fullName}`)}`}
                    className="px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                  >
                    Schedule Team Consultation
                  </a>
                  <button
                    onClick={() => window.print()}
                    className="px-8 py-3 border border-blue-600 text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-colors flex items-center justify-center"
                  >
                    <Download className="h-4 w-4 mr-2" />
                    Print/Save Report
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }
  
  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
      <div className="max-w-4xl mx-auto">
        <div className="bg-white rounded-2xl shadow-2xl overflow-hidden">
          {/* Header */}
          <div className="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-8 text-center">
            <h1 className="text-3xl font-bold mb-2">Team Efficiency Calculator</h1>
            <p className="text-blue-100">
              Analyze specific team inefficiencies and unlock potential savings in UAE operations
            </p>
          </div>
          
          {/* Progress Bar */}
          <div className="bg-gray-200 h-2">
            <div 
              className="bg-gradient-to-r from-blue-500 to-indigo-500 h-full transition-all duration-500"
              style={{ width: `${progress}%` }}
            />
          </div>
          
          {/* Form Content */}
          <div className="p-8">
            {/* Step 0: Contact Information */}
            {currentStep === 0 && (
              <div className="space-y-6">
                <div className="flex items-center mb-6">
                  <User className="h-6 w-6 text-blue-600 mr-3" />
                  <h2 className="text-2xl font-semibold text-gray-900">Contact Information</h2>
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Full Name *
                    </label>
                    <input
                      type="text"
                      value={formData.fullName}
                      onChange={(e) => handleInputChange('fullName', e.target.value)}
                      placeholder="Ahmed Al-Mansouri"
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Company Email Address *
                    </label>
                    <input
                      type="email"
                      value={formData.companyEmail}
                      onChange={(e) => handleInputChange('companyEmail', e.target.value)}
                      placeholder="ahmed@company.ae"
                      className={`w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                        formData.companyEmail && !isCompanyEmail(formData.companyEmail) 
                          ? 'border-red-500' 
                          : 'border-gray-300'
                      }`}
                    />
                    {formData.companyEmail && !isCompanyEmail(formData.companyEmail) && (
                      <p className="mt-1 text-sm text-red-500">Please use your company email address (not Gmail, Yahoo, etc.)</p>
                    )}
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Company Name *
                    </label>
                    <input
                      type="text"
                      value={formData.companyName}
                      onChange={(e) => handleInputChange('companyName', e.target.value)}
                      placeholder="ABC Insurance LLC"
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Mobile Number *
                    </label>
                    <input
                      type="tel"
                      value={formData.mobileNumber}
                      onChange={(e) => handleInputChange('mobileNumber', e.target.value)}
                      placeholder="+971 50 123 4567"
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                </div>
              </div>
            )}
            
            {/* Step 1: Team Selection */}
            {currentStep === 1 && (
              <div className="space-y-6">
                <div className="flex items-center mb-6">
                  <Users className="h-6 w-6 text-blue-600 mr-3" />
                  <h2 className="text-2xl font-semibold text-gray-900">Team Selection</h2>
                </div>
                
                <p className="text-gray-600 mb-6">
                  Select the specific team you want to evaluate for efficiency improvements. 
                  Each team has different tasks and optimization opportunities.
                </p>
                
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  {teams.map(team => (
                    <TeamCard
                      key={team.id}
                      team={team}
                      isSelected={selectedTeam === team.id}
                      onClick={() => setSelectedTeam(team.id)}
                    />
                  ))}
                </div>
              </div>
            )}
            
            {/* Step 2: Role Selection & Team Size */}
            {currentStep === 2 && (
              <div className="space-y-6">
                <div className="flex items-center mb-6">
                  <Building className="h-6 w-6 text-blue-600 mr-3" />
                  <h2 className="text-2xl font-semibold text-gray-900">Role Level & Team Size</h2>
                </div>
                
                <p className="text-gray-600 mb-6">
                  Select the role level that best represents your team members. This helps us provide accurate cost comparisons based on UAE market data.
                </p>
                
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                  {Object.entries(roleDefaults).map(([roleId, roleData]) => (
                    <div
                      key={roleId}
                      className={`p-6 border-2 rounded-xl cursor-pointer transition-all duration-300 ${
                        selectedRole === roleId 
                          ? 'border-blue-500 bg-blue-50 transform scale-105' 
                          : 'border-gray-200 hover:border-blue-300 hover:shadow-md'
                      }`}
                      onClick={() => handleRoleSelection(roleId)}
                    >
                      <h3 className="font-semibold text-gray-900 mb-2">{roleData.name}</h3>
                      <div className="text-sm text-gray-600 space-y-1">
                        <div>Salary: AED {roleData.fullSalary.toLocaleString()}</div>
                        <div>Total Cost: AED {(roleData.fullSalary + roleData.visaCosts + roleData.insurance + roleData.training + roleData.equipment + roleData.officeSpace).toLocaleString()}</div>
                      </div>
                    </div>
                  ))}
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                      Number of Team Members *
                      <Tooltip text="Total number of people in this specific team. Include full-time, part-time, and contract workers." />
                    </label>
                    <input
                      type="number"
                      value={formData.teamSize}
                      onChange={(e) => handleInputChange('teamSize', e.target.value)}
                      placeholder="5"
                      min="1"
                      max="50"
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                      Team Maturity Level *
                      <Tooltip text="How established the team's current processes are. Newer teams may have more automation opportunities." />
                    </label>
                    <select
                      value={formData.teamMaturity}
                      onChange={(e) => handleInputChange('teamMaturity', e.target.value)}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                      <option value="">Select maturity level</option>
                      <option value="1">New team (less than 1 year)</option>
                      <option value="2">Developing (1-3 years)</option>
                      <option value="3">Established (3-7 years)</option>
                      <option value="4">Mature (7+ years)</option>
                    </select>
                  </div>
                </div>
              </div>
            )}
            
            {/* Step 3: Cost Structure */}
            {currentStep === 3 && (
              <div className="space-y-6">
                <div className="flex items-center mb-6">
                  <CreditCard className="h-6 w-6 text-blue-600 mr-3" />
                  <h2 className="text-2xl font-semibold text-gray-900">Employee Cost Structure</h2>
                </div>
                
                <p className="text-gray-600 mb-6">
                  Review and adjust the cost breakdown based on your company's actual expenses. 
                  You can use our UAE market defaults or input your specific costs.
                </p>
                
                <div className="space-y-6">
                  {/* Full Salary */}
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                      Annual Full Salary per Team Member (AED) *
                      <Tooltip text="Complete salary package including HRA, TA and other monthly benefits. This is the total amount paid to the employee monthly × 12." />
                    </label>
                    <input
                      type="number"
                      value={formData.fullSalary}
                      onChange={(e) => handleInputChange('fullSalary', e.target.value)}
                      placeholder={currentRoleDefaults ? currentRoleDefaults.fullSalary.toString() : "48000"}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                    <p className="text-xs text-gray-500 mt-1">Includes HRA, TA and other monthly benefits</p>
                  </div>
                  
                  {/* Additional Annual Costs */}
                  <div>
                    <h3 className="text-lg font-medium text-gray-900 mb-4">Additional Annual Costs (check all that apply):</h3>
                    
                    {/* Visa & Legal Costs */}
                    <div className="mb-4 p-4 border border-gray-200 rounded-lg">
                      <div className="flex items-start justify-between">
                        <div className="flex-1">
                          <h4 className="font-medium text-gray-900">Visa & Legal Costs</h4>
                          <p className="text-sm text-gray-600">Annual visa processing, Emirates ID, medical tests</p>
                        </div>
                        <div className="ml-4 flex items-center space-x-4">
                          <label className="flex items-center">
                            <input
                              type="radio"
                              checked={!formData.visaCostsCustom}
                              onChange={() => handleInputChange('visaCostsCustom', false)}
                              className="mr-2"
                            />
                            <span className="text-sm">Default (AED {currentRoleDefaults?.visaCosts.toLocaleString()})</span>
                          </label>
                          <label className="flex items-center">
                            <input
                              type="radio"
                              checked={formData.visaCostsCustom}
                              onChange={() => handleInputChange('visaCostsCustom', true)}
                              className="mr-2"
                            />
                            <span className="text-sm">Custom:</span>
                            <input
                              type="number"
                              value={formData.visaCostsCustom ? formData.visaCosts : ''}
                              onChange={(e) => handleInputChange('visaCosts', e.target.value)}
                              placeholder="5000"
                              disabled={!formData.visaCostsCustom}
                              className={`ml-2 w-24 px-2 py-1 border border-gray-300 rounded text-sm ${
                                !formData.visaCostsCustom ? 'bg-gray-100' : ''
                              }`}
                            />
                          </label>
                        </div>
                      </div>
                    </div>
                    
                    {/* Medical Insurance */}
                    <div className="mb-4 p-4 border border-gray-200 rounded-lg">
                      <div className="flex items-start justify-between">
                        <div className="flex-1">
                          <h4 className="font-medium text-gray-900">Medical Insurance</h4>
                          <p className="text-sm text-gray-600">Annual medical insurance premium per employee</p>
                        </div>
                        <div className="ml-4 flex items-center space-x-4">
                          <label className="flex items-center">
                            <input
                              type="radio"
                              checked={!formData.insuranceCustom}
                              onChange={() => handleInputChange('insuranceCustom', false)}
                              className="mr-2"
                            />
                            <span className="text-sm">Default (AED {currentRoleDefaults?.insurance.toLocaleString()})</span>
                          </label>
                          <label className="flex items-center">
                            <input
                              type="radio"
                              checked={formData.insuranceCustom}
                              onChange={() => handleInputChange('insuranceCustom', true)}
                              className="mr-2"
                            />
                            <span className="text-sm">Custom:</span>
                            <input
                              type="number"
                              value={formData.insuranceCustom ? formData.insurance : ''}
                              onChange={(e) => handleInputChange('insurance', e.target.value)}
                              placeholder="1899"
                              disabled={!formData.insuranceCustom}
                              className={`ml-2 w-24 px-2 py-1 border border-gray-300 rounded text-sm ${
                                !formData.insuranceCustom ? 'bg-gray-100' : ''
                              }`}
                            />
                          </label>
                        </div>
                      </div>
                    </div>
                    
                    {/* Training & Development */}
                    <div className="mb-4 p-4 border border-gray-200 rounded-lg">
                      <div className="flex items-start justify-between">
                        <div className="flex-1">
                          <h4 className="font-medium text-gray-900">Training & Development</h4>
                          <p className="text-sm text-gray-600">Annual training courses and skill development</p>
                        </div>
                        <div className="ml-4 flex items-center space-x-4">
                          <label className="flex items-center">
                            <input
                              type="radio"
                              checked={!formData.trainingCustom}
                              onChange={() => handleInputChange('trainingCustom', false)}
                              className="mr-2"
                            />
                            <span className="text-sm">Default (AED {currentRoleDefaults?.training.toLocaleString()})</span>
                          </label>
                          <label className="flex items-center">
                            <input
                              type="radio"
                              checked={formData.trainingCustom}
                              onChange={() => handleInputChange('trainingCustom', true)}
                              className="mr-2"
                            />
                            <span className="text-sm">Custom:</span>
                            <input
                              type="number"
                              value={formData.trainingCustom ? formData.training : ''}
                              onChange={(e) => handleInputChange('training', e.target.value)}
                              placeholder="1500"
                              disabled={!formData.trainingCustom}
                              className={`ml-2 w-24 px-2 py-1 border border-gray-300 rounded text-sm ${
                                !formData.trainingCustom ? 'bg-gray-100' : ''
                              }`}
                            />
                          </label>
                        </div>
                      </div>
                    </div>
                    
                    {/* Equipment & Tools */}
                    <div className="mb-4 p-4 border border-gray-200 rounded-lg">
                      <div className="flex items-start justify-between">
                        <div className="flex-1">
                          <h4 className="font-medium text-gray-900">Equipment & Tools</h4>
                          <p className="text-sm text-gray-600">Annual equipment, software licenses, tools</p>
                        </div>
                        <div className="ml-4 flex items-center space-x-4">
                          <label className="flex items-center">
                            <input
                              type="radio"
                              checked={!formData.equipmentCustom}
                              onChange={() => handleInputChange('equipmentCustom', false)}
                              className="mr-2"
                            />
                            <span className="text-sm">Default (AED {currentRoleDefaults?.equipment.toLocaleString()})</span>
                          </label>
                          <label className="flex items-center">
                            <input
                              type="radio"
                              checked={formData.equipmentCustom}
                              onChange={() => handleInputChange('equipmentCustom', true)}
                              className="mr-2"
                            />
                            <span className="text-sm">Custom:</span>
                            <input
                              type="number"
                              value={formData.equipmentCustom ? formData.equipment : ''}
                              onChange={(e) => handleInputChange('equipment', e.target.value)}
                              placeholder="4000"
                              disabled={!formData.equipmentCustom}
                              className={`ml-2 w-24 px-2 py-1 border border-gray-300 rounded text-sm ${
                                !formData.equipmentCustom ? 'bg-gray-100' : ''
                              }`}
                            />
                          </label>
                        </div>
                      </div>
                    </div>
                    
                    {/* Office Space Allocation */}
                    <div className="mb-4 p-4 border border-gray-200 rounded-lg">
                      <div className="flex items-start justify-between">
                        <div className="flex-1">
                          <h4 className="font-medium text-gray-900">Office Space Allocation</h4>
                          <p className="text-sm text-gray-600">Annual office rent, utilities allocated per employee</p>
                        </div>
                        <div className="ml-4 flex items-center space-x-4">
                          <label className="flex items-center">
                            <input
                              type="radio"
                              checked={!formData.officeSpaceCustom}
                              onChange={() => handleInputChange('officeSpaceCustom', false)}
                              className="mr-2"
                            />
                            <span className="text-sm">Default (AED {currentRoleDefaults?.officeSpace.toLocaleString()})</span>
                          </label>
                          <label className="flex items-center">
                            <input
                              type="radio"
                              checked={formData.officeSpaceCustom}
                              onChange={() => handleInputChange('officeSpaceCustom', true)}
                              className="mr-2"
                            />
                            <span className="text-sm">Custom:</span>
                            <input
                              type="number"
                              value={formData.officeSpaceCustom ? formData.officeSpace : ''}
                              onChange={(e) => handleInputChange('officeSpace', e.target.value)}
                              placeholder="12000"
                              disabled={!formData.officeSpaceCustom}
                              className={`ml-2 w-24 px-2 py-1 border border-gray-300 rounded text-sm ${
                                !formData.officeSpaceCustom ? 'bg-gray-100' : ''
                              }`}
                            />
                          </label>
                        </div>
                      </div>
                    </div>
                    
                    {/* EOS/Gratuity */}
                    <div className="mb-4 p-4 border border-gray-200 rounded-lg">
                      <div className="flex items-start justify-between">
                        <div className="flex-1">
                          <h4 className="font-medium text-gray-900">EOS/Gratuity Provision</h4>
                          <p className="text-sm text-gray-600">Based on UAE Labor Law: 21 working days per year</p>
                        </div>
                        <div className="ml-4 flex items-center space-x-4">
                          <label className="flex items-center">
                            <input
                              type="radio"
                              checked={!formData.eosGratuityCustom}
                              onChange={() => handleInputChange('eosGratuityCustom', false)}
                              className="mr-2"
                            />
                            <span className="text-sm">Auto (AED {calculateEmployeeCost().autoEosGratuity?.toLocaleString() || '0'})</span>
                          </label>
                          <label className="flex items-center">
                            <input
                              type="radio"
                              checked={formData.eosGratuityCustom}
                              onChange={() => handleInputChange('eosGratuityCustom', true)}
                              className="mr-2"
                            />
                            <span className="text-sm">Custom:</span>
                            <input
                              type="number"
                              value={formData.eosGratuityCustom ? formData.eosGratuity : ''}
                              onChange={(e) => handleInputChange('eosGratuity', e.target.value)}
                              placeholder={calculateEmployeeCost().autoEosGratuity.toString()}
                              disabled={!formData.eosGratuityCustom}
                              className={`ml-2 w-24 px-2 py-1 border border-gray-300 rounded text-sm ${
                                !formData.eosGratuityCustom ? 'bg-gray-100' : ''
                              }`}
                            />
                          </label>
                        </div>
                      </div>
                      <div className="mt-2 text-xs text-blue-600">
                        📘 Calculation: Annual Salary ÷ 365 × 21 days = AED {calculateEmployeeCost().autoEosGratuity?.toLocaleString() || '0'}
                      </div>
                    </div>
                    
                    {/* Other Annual Costs */}
                    <div className="mb-4 p-4 border border-gray-200 rounded-lg">
                      <div className="flex items-start justify-between">
                        <div className="flex-1">
                          <h4 className="font-medium text-gray-900">Other Annual Costs</h4>
                          <p className="text-sm text-gray-600">Any additional costs (recruitment, gratuity, etc.)</p>
                        </div>
                        <div className="ml-4">
                          <input
                            type="number"
                            value={formData.otherCosts}
                            onChange={(e) => handleInputChange('otherCosts', e.target.value)}
                            placeholder="0"
                            className="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  {/* Cost Summary */}
                  <div className="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h3 className="font-medium text-green-900 mb-2">Total Cost Summary</h3>
                    <div className="text-sm text-green-800 space-y-1">
                      <div className="flex justify-between">
                        <span>Full Salary:</span>
                        <span>AED {calculateEmployeeCost().fullSalary.toLocaleString()}</span>
                      </div>
                      <div className="flex justify-between">
                        <span>Total Overheads:</span>
                        <span>AED {calculateEmployeeCost().totalOverheads.toLocaleString()}</span>
                      </div>
                      <div className="flex justify-between font-semibold border-t pt-1">
                        <span>True Cost per Employee:</span>
                        <span>AED {calculateEmployeeCost().trueCost.toLocaleString()}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            )}
            
            {/* Step 4: Task Allocation */}
            {currentStep === 4 && currentTeam && (
              <div className="space-y-6">
                <div className="flex items-center mb-6">
                  <Clock className="h-6 w-6 text-blue-600 mr-3" />
                  <h2 className="text-2xl font-semibold text-gray-900">
                    {currentTeam.name} Time Allocation
                  </h2>
                </div>
                
                <p className="text-gray-600 mb-6">
                  Estimate what percentage of a typical work week your team spends on each task. 
                  The total should add up to approximately 100%.
                </p>
                
                <div className={`p-4 rounded-lg text-center font-semibold ${
                  totalAllocation >= 95 && totalAllocation <= 105 
                    ? 'bg-green-50 border border-green-200 text-green-800'
                    : totalAllocation < 80 || totalAllocation > 120
                    ? 'bg-red-50 border border-red-200 text-red-800'
                    : 'bg-yellow-50 border border-yellow-200 text-yellow-800'
                }`}>
                  Total Time Allocation: {totalAllocation}%
                  {totalAllocation >= 95 && totalAllocation <= 105 && (
                    <div className="flex items-center justify-center mt-1">
                      <CheckCircle className="h-4 w-4 mr-1" />
                      Time allocation looks realistic
                    </div>
                  )}
                  {(totalAllocation < 80 || totalAllocation > 120) && (
                    <div className="flex items-center justify-center mt-1">
                      <AlertCircle className="h-4 w-4 mr-1" />
                      Total time allocation seems unrealistic
                    </div>
                  )}
                </div>
                
                <div className="space-y-4">
                  {currentTeam.tasks.map(task => (
                    <TaskSlider
                      key={task.id}
                      task={task}
                      value={taskAllocations[task.id] || task.default}
                      onChange={handleTaskAllocation}
                    />
                  ))}
                </div>
              </div>
            )}
            
            {/* Step 5: Team Diagnostic */}
            {currentStep === 5 && currentTeam && (
              <div className="space-y-6">
                <div className="flex items-center mb-6">
                  <AlertCircle className="h-6 w-6 text-blue-600 mr-3" />
                  <h2 className="text-2xl font-semibold text-gray-900">{currentTeam.name} Efficiency Diagnostic</h2>
                </div>
                
                <p className="text-gray-600 mb-6">
                  Answer these questions about your {currentTeam.name.toLowerCase()} to help us identify specific inefficiencies and time-wasting activities.
                </p>
                
                {teamQuestions[currentTeam.id] && (
                  <div className="space-y-6">
                    {teamQuestions[currentTeam.id].map((question, questionIndex) => (
                      <div key={questionIndex} className="p-6 border border-gray-200 rounded-lg">
                        <div className="flex items-start mb-4">
                          <span className="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center text-sm font-semibold mr-4 mt-1">
                            {questionIndex + 1}
                          </span>
                          <div className="flex-1">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">{question.question}</h3>
                            <div className="space-y-3">
                              {question.options.map((option, optionIndex) => (
                                <label
                                  key={optionIndex}
                                  className={`flex items-start p-3 border rounded-lg cursor-pointer transition-all duration-200 ${
                                    formData.diagnosticAnswers[questionIndex] === optionIndex
                                      ? 'border-blue-500 bg-blue-50'
                                      : 'border-gray-200 hover:border-blue-300'
                                  }`}
                                >
                                  <input
                                    type="radio"
                                    name={`question-${questionIndex}`}
                                    checked={formData.diagnosticAnswers[questionIndex] === optionIndex}
                                    onChange={() => handleDiagnosticAnswer(questionIndex, optionIndex)}
                                    className="mt-1 mr-3"
                                  />
                                  <div className="flex-1">
                                    <div className="font-medium text-gray-900">{option.label}</div>
                                    {option.time_loss > 0 && (
                                      <div className="text-sm text-gray-500 mt-1">
                                        Estimated time impact: {option.time_loss} minutes/week
                                      </div>
                                    )}
                                  </div>
                                </label>
                              ))}
                            </div>
                          </div>
                        </div>
                      </div>
                    ))}
                    
                    {/* Live diagnostic preview */}
                    {Object.keys(formData.diagnosticAnswers).length > 0 && (
                      <div className="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 className="font-medium text-blue-900 mb-2">Live Diagnostic Preview</h3>
                        <div className="text-sm text-blue-800">
                          <div>Inefficiency Score: {Math.round(calculateDiagnosticResults().inefficiencyPercent * 100)}%</div>
                          <div>Weekly Time Waste: {calculateDiagnosticResults().timeWasteHours} hours per employee</div>
                          {calculateDiagnosticResults().keyIssues.length > 0 && (
                            <div className="mt-2">
                              Key Issues: {calculateDiagnosticResults().keyIssues.map(issue => 
                                `${issue.area} (${issue.impact})`
                              ).join(', ')}
                            </div>
                          )}
                        </div>
                      </div>
                    )}
                    
                    {/* Validation reminder */}
                    {teamQuestions[currentTeam.id] && !validateStep(5) && (
                      <div className="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div className="flex items-center">
                          <AlertTriangle className="h-5 w-5 text-yellow-500 mr-2" />
                          <span className="text-sm text-yellow-800">
                            Please answer all {teamQuestions[currentTeam.id].length} questions to proceed to the next step.
                          </span>
                        </div>
                      </div>
                    )}
                  </div>
                )}
              </div>
            )}
            
            {/* Step 6: Improvement Goals */}
            {currentStep === 6 && (
              <div className="space-y-6">
                <div className="flex items-center mb-6">
                  <Target className="h-6 w-6 text-blue-600 mr-3" />
                  <h2 className="text-2xl font-semibold text-gray-900">Team Improvement Goals</h2>
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                      Primary Goal for This Team *
                      <Tooltip text="The main improvement objective helps prioritize which efficiency gains will have the most impact." />
                    </label>
                    <select
                      value={formData.primaryGoal}
                      onChange={(e) => handleInputChange('primaryGoal', e.target.value)}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                      <option value="">Select primary goal</option>
                      <option value="reduce_costs">Reduce operational costs</option>
                      <option value="increase_speed">Increase processing speed</option>
                      <option value="improve_quality">Improve work quality</option>
                      <option value="reduce_errors">Reduce errors and rework</option>
                      <option value="scale_capacity">Scale team capacity</option>
                      <option value="improve_satisfaction">Improve team satisfaction</option>
                    </select>
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                      Target Efficiency Improvement *
                      <Tooltip text="Realistic target for reducing time spent on routine tasks. Conservative: 10-20%, Moderate: 20-30%, Aggressive: 30%+" />
                    </label>
                    <select
                      value={formData.targetEfficiency}
                      onChange={(e) => handleInputChange('targetEfficiency', e.target.value)}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                      <option value="">Select target</option>
                      <option value="15">Conservative (10-20% improvement)</option>
                      <option value="25">Moderate (20-30% improvement)</option>
                      <option value="35">Aggressive (30-40% improvement)</option>
                      <option value="45">Transformational (40%+ improvement)</option>
                    </select>
                  </div>
                  
                  <div className="md:col-span-2">
                    <label className="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                      Implementation Timeline *
                      <Tooltip text="Realistic timeframe for implementing efficiency improvements. Faster implementation requires more resources but delivers quicker ROI." />
                    </label>
                    <select
                      value={formData.timeline}
                      onChange={(e) => handleInputChange('timeline', e.target.value)}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                      <option value="">Select timeline</option>
                      <option value="3">Quick wins (1-3 months)</option>
                      <option value="6">Standard rollout (3-6 months)</option>
                      <option value="12">Comprehensive (6-12 months)</option>
                      <option value="18">Strategic (12+ months)</option>
                    </select>
                  </div>
                </div>
              </div>
            )}
            
            {/* Navigation Buttons */}
            <div className="flex justify-between mt-8 pt-6 border-t border-gray-200">
              <button
                onClick={prevStep}
                disabled={currentStep === 0}
                className={`px-6 py-3 rounded-lg font-semibold transition-colors ${
                  currentStep === 0
                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Previous
              </button>
              
              {currentStep < totalSteps - 1 ? (
                <button
                  onClick={nextStep}
                  disabled={!validateStep(currentStep)}
                  className={`px-6 py-3 rounded-lg font-semibold transition-colors ${
                    validateStep(currentStep)
                      ? 'bg-blue-600 text-white hover:bg-blue-700'
                      : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                  }`}
                >
                  Next Step
                  <ChevronRight className="h-4 w-4 ml-2 inline" />
                </button>
              ) : (
                <button
                  onClick={handleSubmit}
                  disabled={!validateStep(currentStep)}
                  className={`px-8 py-3 rounded-lg font-semibold transition-colors ${
                    validateStep(currentStep)
                      ? 'bg-green-600 text-white hover:bg-green-700'
                      : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                  }`}
                >
                  Analyze Team Efficiency
                </button>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default TeamEfficiencyCalculator;
