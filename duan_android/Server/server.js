const express = require('express');
const cors = require('cors');
const { connect } = require('./config/db/config');  // Import kết nối
const app = express();
const port = 3000;

// Kiểm tra kết nối cơ sở dữ liệu
connect();

// Middleware
app.use(cors()); 
app.use(express.json());

// Import các route
const getStudentRoute = require('./routes/getStudent');
const gpa = require('./routes/gpa');

// Sử dụng các route
app.use('/api/getstudent', getStudentRoute);   // Route lấy sinh viên
app.use('/api/calculate', gpa); // Route tính điểm gpa

//Chạy server ở IP và Port 3000
app.listen(port, '0.0.0.0', () => {
  // Lấy địa chỉ IP cục bộ của server
  const os = require('os');
  const networkInterfaces = os.networkInterfaces();
  const localIp = networkInterfaces['Wi-Fi']?.find(details => details.family === 'IPv4')?.address || 'localhost';
  
  console.log(`Server is running on http://${localIp}:${port}`);
});


