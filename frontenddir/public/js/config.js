// Change to use environment variable or EC2 IP
const API_URL = window.location.hostname === 'localhost' 
    ? 'http://localhost:8080/api'
    : 'http://13.62.253.76:8080/api';  // Your EC2 IP
