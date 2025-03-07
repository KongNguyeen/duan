const express = require('express');
const router = express.Router();
const { pool } = require('../config/db/config');

// API lấy sinh viên theo mssv và học kỳ
router.get('/:mssv/:classYear', async (req, res) => {
  const mssv = req.params.mssv;           // Nhận MSSV từ params
  const classYear = req.params.classYear; // Nhận ClassYear từ params

  console.log(`Received request for MSSV: ${mssv}, Class Year: ${classYear}`); // Xem thông tin nhận được

  try {
    const result = await pool.query(`
      SELECT 
        s.StudentName AS student_name, 
        s.StudentID, 
        s.StudentClass,
        s.Department,
        sub.SubjectID, 
        sub.SubjectName, 
        sg.Grades,
        c.ClassID,
        c.ClassYear
      FROM 
        Student s
      JOIN 
        StudentGrades sg ON s.StudentID = sg.StudentID
      JOIN 
        Subject sub ON sg.SubjectID = sub.SubjectID 
      JOIN 
        Class c ON sg.ClassID = c.ClassID 
      WHERE 
        s.StudentID = $1
        AND c.ClassYear = $2
    `, [mssv, classYear]);

    // Xem kết quả trả về
    console.log('Query Result:', result.rows); 

    if (result.rows.length > 0) {
      res.json(result.rows); // Trả về danh sách tất cả môn học và điểm của sinh viên
    } else {
      console.log(`No grades found for MSSV: ${mssv}, Class Year: ${classYear}`);
      res.status(404).json({ error: 'No grades found for the given student or year' });
    }
  } catch (err) {
    console.error('Error during database query:', err.message); // Lỗi
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;
