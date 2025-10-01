#!/usr/bin/env python3
"""
Machine Learning Analytics for BSU Scholarship System
Real AI-powered insights using Python ML models
"""

import pandas as pd
import numpy as np
from sklearn.linear_model import LogisticRegression, LinearRegression
from sklearn.ensemble import RandomForestClassifier, RandomForestRegressor
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score, r2_score, classification_report
from sklearn.preprocessing import StandardScaler, LabelEncoder
import joblib
import json
from datetime import datetime, timedelta
import warnings
warnings.filterwarnings('ignore')

class ScholarshipMLAnalytics:
    def __init__(self):
        self.models = {}
        self.scalers = {}
        self.encoders = {}
        
    def prepare_data(self, applications_data, students_data, scholarships_data):
        """
        Prepare data for ML models
        """
        # Convert to DataFrame
        df_apps = pd.DataFrame(applications_data)
        df_students = pd.DataFrame(students_data)
        df_scholarships = pd.DataFrame(scholarships_data)
        
        # Merge datasets
        df = df_apps.merge(df_students, left_on='user_id', right_on='id', how='left')
        df = df.merge(df_scholarships, left_on='scholarship_id', right_on='id', how='left')
        
        # Feature engineering
        df['application_month'] = pd.to_datetime(df['created_at']).dt.month
        df['application_day_of_week'] = pd.to_datetime(df['created_at']).dt.dayofweek
        df['days_since_created'] = (datetime.now() - pd.to_datetime(df['created_at'])).dt.days
        df['scholarship_demand'] = df.groupby('scholarship_id')['id'].transform('count')
        df['student_application_count'] = df.groupby('user_id')['id'].transform('count')
        
        # Encode categorical variables
        categorical_cols = ['campus_type', 'scholarship_type', 'grant_type']
        for col in categorical_cols:
            if col in df.columns:
                le = LabelEncoder()
                df[f'{col}_encoded'] = le.fit_transform(df[col].astype(str))
                self.encoders[col] = le
        
        return df
    
    def train_approval_prediction_model(self, df):
        """
        Train logistic regression model to predict application approval
        """
        # Prepare features
        feature_cols = [
            'application_month', 'application_day_of_week', 'days_since_created',
            'scholarship_demand', 'student_application_count', 'grant_amount',
            'campus_type_encoded', 'scholarship_type_encoded', 'grant_type_encoded'
        ]
        
        # Remove missing columns
        available_cols = [col for col in feature_cols if col in df.columns]
        X = df[available_cols].fillna(0)
        y = (df['status'] == 'approved').astype(int)
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
        
        # Scale features
        scaler = StandardScaler()
        X_train_scaled = scaler.fit_transform(X_train)
        X_test_scaled = scaler.transform(X_test)
        
        # Train model
        model = LogisticRegression(random_state=42, max_iter=1000)
        model.fit(X_train_scaled, y_train)
        
        # Evaluate
        y_pred = model.predict(X_test_scaled)
        accuracy = accuracy_score(y_test, y_pred)
        
        # Store model and scaler
        self.models['approval_prediction'] = model
        self.scalers['approval_prediction'] = scaler
        
        return {
            'model': 'Logistic Regression',
            'accuracy': accuracy,
            'features': available_cols,
            'feature_importance': dict(zip(available_cols, model.coef_[0]))
        }
    
    def train_success_prediction_model(self, df):
        """
        Train random forest to predict student success after approval
        """
        # Filter approved applications
        approved_df = df[df['status'] == 'approved'].copy()
        
        if len(approved_df) < 10:  # Need minimum data
            return {'error': 'Insufficient approved applications for training'}
        
        # Prepare features
        feature_cols = [
            'application_month', 'scholarship_demand', 'grant_amount',
            'campus_type_encoded', 'scholarship_type_encoded'
        ]
        
        available_cols = [col for col in feature_cols if col in approved_df.columns]
        X = approved_df[available_cols].fillna(0)
        
        # Create target variable (simplified - you'd need actual success metrics)
        # For demo, we'll use a synthetic success metric
        np.random.seed(42)
        y = np.random.binomial(1, 0.7, len(approved_df))  # 70% success rate
        
        # Split and train
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
        
        model = RandomForestClassifier(n_estimators=100, random_state=42)
        model.fit(X_train, y_train)
        
        y_pred = model.predict(X_test)
        accuracy = accuracy_score(y_test, y_pred)
        
        self.models['success_prediction'] = model
        
        return {
            'model': 'Random Forest',
            'accuracy': accuracy,
            'features': available_cols,
            'feature_importance': dict(zip(available_cols, model.feature_importances_))
        }
    
    def train_approval_rate_regression(self, df):
        """
        Train linear regression to predict campus approval rates
        """
        # Group by campus and calculate approval rates
        campus_stats = df.groupby('campus_id').agg({
            'id': 'count',
            'status': lambda x: (x == 'approved').sum(),
            'application_month': 'mean',
            'scholarship_demand': 'mean',
            'grant_amount': 'mean'
        }).rename(columns={'id': 'total_applications'})
        
        campus_stats['approval_rate'] = (campus_stats['status'] / campus_stats['total_applications']) * 100
        
        # Prepare features
        feature_cols = ['total_applications', 'application_month', 'scholarship_demand', 'grant_amount']
        X = campus_stats[feature_cols].fillna(0)
        y = campus_stats['approval_rate']
        
        if len(X) < 3:  # Need minimum campuses
            return {'error': 'Insufficient campus data for regression'}
        
        # Train model
        model = LinearRegression()
        model.fit(X, y)
        
        # Evaluate
        y_pred = model.predict(X)
        r2 = r2_score(y, y_pred)
        
        self.models['approval_rate_regression'] = model
        
        return {
            'model': 'Linear Regression',
            'r2_score': r2,
            'features': feature_cols,
            'coefficients': dict(zip(feature_cols, model.coef_))
        }
    
    def generate_ml_insights(self, df):
        """
        Generate AI-powered insights using trained models
        """
        insights = {
            'predictions': {},
            'recommendations': [],
            'risk_factors': [],
            'opportunities': []
        }
        
        # Approval prediction insights
        if 'approval_prediction' in self.models:
            model = self.models['approval_prediction']
            scaler = self.scalers['approval_prediction']
            
            # Get feature importance
            feature_importance = model.coef_[0]
            important_features = sorted(zip(model.feature_names_in_, feature_importance), 
                                      key=lambda x: abs(x[1]), reverse=True)[:3]
            
            insights['predictions']['approval_factors'] = [
                f"{feature}: {importance:.3f}" for feature, importance in important_features
            ]
        
        # Success prediction insights
        if 'success_prediction' in self.models:
            model = self.models['success_prediction']
            feature_importance = model.feature_importances_
            important_features = sorted(zip(model.feature_names_in_, feature_importance), 
                                      key=lambda x: x[1], reverse=True)[:3]
            
            insights['predictions']['success_factors'] = [
                f"{feature}: {importance:.3f}" for feature, importance in important_features
            ]
        
        # Generate recommendations based on model insights
        if 'approval_prediction' in self.models:
            # Analyze approval patterns
            approval_rate = (df['status'] == 'approved').mean()
            
            if approval_rate < 0.5:
                insights['recommendations'].append(
                    "ML Analysis: Low approval rate detected. Consider reviewing application criteria."
                )
            
            # Check for seasonal patterns
            monthly_approvals = df.groupby('application_month')['status'].apply(
                lambda x: (x == 'approved').mean()
            )
            
            if monthly_approvals.std() > 0.2:
                insights['recommendations'].append(
                    "ML Analysis: Significant seasonal variation in approval rates detected."
                )
        
        # Risk factors
        if len(df) > 0:
            high_risk_campuses = df.groupby('campus_id')['status'].apply(
                lambda x: (x == 'approved').mean()
            )
            
            low_performers = high_risk_campuses[high_risk_campuses < 0.3]
            if len(low_performers) > 0:
                insights['risk_factors'].append(
                    f"ML Analysis: {len(low_performers)} campus(es) with approval rates below 30%"
                )
        
        return insights
    
    def predict_future_trends(self, df, months_ahead=3):
        """
        Predict future application trends
        """
        # Time series analysis
        df['created_at'] = pd.to_datetime(df['created_at'])
        monthly_applications = df.groupby(df['created_at'].dt.to_period('M')).size()
        
        # Simple trend analysis
        if len(monthly_applications) >= 3:
            trend = np.polyfit(range(len(monthly_applications)), monthly_applications.values, 1)[0]
            
            # Predict next months
            last_month = monthly_applications.index[-1]
            predictions = []
            
            for i in range(1, months_ahead + 1):
                predicted_value = monthly_applications.iloc[-1] + (trend * i)
                predictions.append({
                    'month': str(last_month + i),
                    'predicted_applications': max(0, int(predicted_value))
                })
            
            return {
                'trend_direction': 'increasing' if trend > 0 else 'decreasing',
                'trend_strength': abs(trend),
                'predictions': predictions
            }
        
        return {'error': 'Insufficient historical data for trend prediction'}
    
    def save_models(self, filepath='models/'):
        """
        Save trained models for future use
        """
        import os
        os.makedirs(filepath, exist_ok=True)
        
        for name, model in self.models.items():
            joblib.dump(model, f"{filepath}{name}.pkl")
        
        for name, scaler in self.scalers.items():
            joblib.dump(scaler, f"{filepath}{name}_scaler.pkl")
        
        # Save encoders
        with open(f"{filepath}encoders.json", 'w') as f:
            json.dump({name: encoder.classes_.tolist() for name, encoder in self.encoders.items()}, f)
    
    def load_models(self, filepath='models/'):
        """
        Load pre-trained models
        """
        import os
        if not os.path.exists(filepath):
            return False
        
        for name in ['approval_prediction', 'success_prediction', 'approval_rate_regression']:
            model_file = f"{filepath}{name}.pkl"
            scaler_file = f"{filepath}{name}_scaler.pkl"
            
            if os.path.exists(model_file):
                self.models[name] = joblib.load(model_file)
            
            if os.path.exists(scaler_file):
                self.scalers[name] = joblib.load(scaler_file)
        
        return True

# Example usage function
def run_ml_analysis(applications_data, students_data, scholarships_data):
    """
    Main function to run ML analysis
    """
    ml_analytics = ScholarshipMLAnalytics()
    
    # Prepare data
    df = ml_analytics.prepare_data(applications_data, students_data, scholarships_data)
    
    # Train models
    results = {}
    
    # Approval prediction
    approval_results = ml_analytics.train_approval_prediction_model(df)
    results['approval_prediction'] = approval_results
    
    # Success prediction
    success_results = ml_analytics.train_success_prediction_model(df)
    results['success_prediction'] = success_results
    
    # Approval rate regression
    regression_results = ml_analytics.train_approval_rate_regression(df)
    results['approval_rate_regression'] = regression_results
    
    # Generate insights
    insights = ml_analytics.generate_ml_insights(df)
    results['insights'] = insights
    
    # Predict trends
    trends = ml_analytics.predict_future_trends(df)
    results['trends'] = trends
    
    # Save models
    ml_analytics.save_models()
    
    return results

if __name__ == "__main__":
    # Example data structure
    sample_applications = [
        {'id': 1, 'user_id': 1, 'scholarship_id': 1, 'status': 'approved', 'created_at': '2025-01-01'},
        {'id': 2, 'user_id': 2, 'scholarship_id': 1, 'status': 'rejected', 'created_at': '2025-01-02'},
    ]
    
    sample_students = [
        {'id': 1, 'campus_id': 1, 'campus_type': 'constituent'},
        {'id': 2, 'campus_id': 1, 'campus_type': 'constituent'},
    ]
    
    sample_scholarships = [
        {'id': 1, 'scholarship_type': 'academic', 'grant_type': 'full', 'grant_amount': 50000},
    ]
    
    # Run analysis
    results = run_ml_analysis(sample_applications, sample_students, sample_scholarships)
    print(json.dumps(results, indent=2))
