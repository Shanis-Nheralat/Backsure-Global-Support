# Team Efficiency Calculator

A comprehensive React application for analyzing team inefficiencies and calculating potential cost savings with BSG (Business Support Group) services.

## Features

- **Multi-currency Support**: AED, USD, EUR, GBP, SAR, QAR
- **6 Team Types**: Sales, Underwriting, Claims, Customer Service, Finance, Compliance
- **Role-based Cost Calculation**: Junior, Mid-level, Senior roles with realistic overhead costs
- **Diagnostic Assessment**: Team-specific efficiency questionnaires
- **Professional Reports**: Printable PDF-ready business reports
- **Responsive Design**: Mobile-friendly interface
- **Error Handling**: Robust error boundaries and validation

## Prerequisites

- Node.js (v16 or higher)
- npm or yarn package manager
- Modern web browser

## Installation

### 1. Clone the Repository
```bash
git clone <your-repository-url>
cd team-efficiency-calculator
```

### 2. Install Dependencies
```bash
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
```
Edit `.env` file with your configuration if needed.

### 4. Install Required Dependencies
```bash
# Core dependencies
npm install react react-dom react-scripts

# UI Components
npm install lucide-react

# Styling
npm install -D tailwindcss autoprefixer postcss @tailwindcss/forms

# Initialize Tailwind CSS
npx tailwindcss init -p
```

## Project Structure

```
team-efficiency-calculator/
├── public/
│   ├── index.html
│   └── favicon.ico
├── src/
│   ├── components/
│   │   ├── TeamEfficiencyCalculator.jsx  # Main component
│   │   └── ErrorBoundary.jsx             # Error handling
│   ├── constants/
│   │   └── calculatorConstants.js        # Configuration data
│   ├── utils/
│   │   └── calculatorUtils.js            # Helper functions
│   ├── App.js                           # Main app component
│   ├── index.js                         # Entry point
│   └── index.css                        # Global styles
├── package.json
├── tailwind.config.js
├── postcss.config.js
├── .env.example
└── README.md
```

## Configuration Files

### 1. Tailwind CSS Configuration
The `tailwind.config.js` includes custom theme extensions:
- Custom scales and animations
- Extended color palette
- Responsive breakpoints
- Print-specific styles

### 2. PostCSS Configuration
Standard configuration for Tailwind CSS processing.

### 3. Package.json Scripts
```json
{
  "scripts": {
    "start": "react-scripts start",
    "build": "react-scripts build",
    "test": "react-scripts test",
    "eject": "react-scripts eject"
  }
}
```

## Usage

### Development Mode
```bash
npm start
```
Opens the application at `http://localhost:3000`

### Production Build
```bash
npm run build
```
Creates an optimized production build in the `build/` folder.

## Component Architecture

### Main Component: TeamEfficiencyCalculator
- **7-step wizard interface**
- **State management** with React hooks
- **Form validation** at each step
- **Real-time calculations**
- **Professional report generation**

### Key Features:

#### Step 1: Contact Information
- Multi-currency selection
- Company email validation
- Mobile number validation

#### Step 2: Team Selection
- 6 predefined team types
- Team-specific BSG services
- Visual selection interface

#### Step 3: Role & Team Size
- 3 role levels with currency-specific defaults
- Dynamic cost calculations
- Team size configuration

#### Step 4: Cost Breakdown
- Comprehensive overhead categories
- Default vs. custom cost options
- Real-time cost summary

#### Step 5: Task Allocation
- Team-specific task breakdown
- Percentage allocation with validation
- Time allocation analysis

#### Step 6: Diagnostic Assessment
- Team-specific questionnaires
- Weighted scoring system
- Inefficiency identification

#### Step 7: Goals & Timeline
- Optimization objectives
- Target efficiency levels
- Implementation timelines

## Calculation Methodology

### Cost Calculation
```javascript
trueCost = baseSalary + (visa + insurance + training + equipment + office + eos + other)
```

### EOS Gratuity (UAE Labor Law)
```javascript
eosGratuity = (annualSalary / 365) * 21
```

### Efficiency Scoring
- Diagnostic assessment (0-20% inefficiency)
- Team maturity factor
- BSG guaranteed 96%+ efficiency

### ROI Calculation
```javascript
roi = ((currentCost - bsgCost) / bsgCost) * 100
```

## Customization

### Adding New Teams
1. Add team configuration to `TEAMS` in `calculatorConstants.js`
2. Add diagnostic questions to `TEAM_QUESTIONS`
3. Define team-specific tasks and BSG services

### Adding New Currencies
1. Add currency to `CURRENCIES` object
2. Update role salary and overhead defaults
3. Test formatting functions

### Modifying Cost Categories
1. Update `COST_CATEGORIES` configuration
2. Modify form validation logic
3. Update calculation functions

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Print Functionality

The application includes comprehensive print styles:
- Optimized layouts for A4 printing
- Color preservation for professional reports
- Page break optimization
- Print-specific styling

## Error Handling

### Error Boundary
- Catches JavaScript errors
- Provides user-friendly error messages
- Development vs. production error display
- Error reporting functionality

### Form Validation
- Real-time field validation
- Step-by-step completion checking
- User-friendly error messages

## Performance Considerations

### Optimization Features
- React.memo for expensive calculations
- Efficient state management
- Lazy loading for large components
- Optimized re-renders

### Bundle Size
- Tree-shaking enabled
- Production build optimization
- Minimal external dependencies

## Deployment

### Build for Production
```bash
npm run build
```

### Static Hosting
The built application can be deployed to:
- Netlify
- Vercel
- GitHub Pages
- AWS S3
- Any static hosting service

### Environment Variables
Configure production environment variables:
```bash
REACT_APP_CONTACT_EMAIL=your-email@domain.com
NODE_ENV=production
```

## Troubleshooting

### Common Issues

1. **Tailwind styles not loading**
   - Ensure PostCSS configuration is correct
   - Check Tailwind CSS imports in index.css

2. **Component not rendering**
   - Check console for JavaScript errors
   - Verify all dependencies are installed

3. **Print layout issues**
   - Test in different browsers
   - Check print-specific CSS media queries

4. **Calculation errors**
   - Verify input validation
   - Check utility function implementations

### Debug Mode
Set `REACT_APP_ENABLE_DIAGNOSTICS=true` to enable:
- Console logging of calculations
- Diagnostic test functions
- Enhanced error reporting

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is proprietary software. All rights reserved.

## Support

For technical support or questions:
- Email: support@bsgsupport.com
- Documentation: [Internal Wiki/Docs]

## Changelog

### v1.0.0
- Initial release
- Multi-currency support
- 6 team types with diagnostic assessments
- Professional report generation
- Print functionality
- Error handling and validation

---

**Built with React + Tailwind CSS for BSG**