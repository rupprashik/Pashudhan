#!/bin/bash

# Backend Startup Script for EC2
# This script builds and runs the Spring Boot application

echo "🚀 Starting Veterinary Clinic Backend..."

# Navigate to backend directory
cd /home/ec2-user/vet-clinic-aws/backend || exit

# Stop any existing Java process
echo "📛 Stopping existing processes..."
pkill -f 'vet-clinic-0.0.1-SNAPSHOT.jar'

# Build the application
echo "🔨 Building application..."
./mvnw clean package -DskipTests

# Check if build was successful
if [ $? -ne 0 ]; then
    echo "❌ Build failed!"
    exit 1
fi

echo "✅ Build successful!"

# Set production profile and environment variables
export SPRING_PROFILES_ACTIVE=prod
export DB_HOST=${DB_HOST:-localhost}
export DB_PORT=${DB_PORT:-3306}
export DB_NAME=${DB_NAME:-vetclinic}
export DB_USER=${DB_USER:-admin}
export DB_PASSWORD=${DB_PASSWORD:-password}
export SERVER_PORT=${SERVER_PORT:-8080}
export CORS_ORIGINS=${CORS_ORIGINS:-http://localhost:3000}

# Run the application in background
echo "🏃 Starting application..."
nohup java -jar target/vet-clinic-0.0.1-SNAPSHOT.jar > app.log 2>&1 &

echo "✅ Application started! PID: $!"
echo "📝 Logs: tail -f /home/ec2-user/vet-clinic-aws/backend/app.log"
echo "🌐 API: http://localhost:8080/api"
