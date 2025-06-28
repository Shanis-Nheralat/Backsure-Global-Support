import React from 'react';
import './index.css';
import TeamEfficiencyCalculator from './components/TeamEfficiencyCalculator';
import ErrorBoundary from './components/ErrorBoundary';

function App() {
  return (
    <ErrorBoundary>
      <div className="App">
        <TeamEfficiencyCalculator />
      </div>
    </ErrorBoundary>
  );
}

export default App;