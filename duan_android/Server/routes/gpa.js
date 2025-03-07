const express = require('express');
const router = express.Router();

// Route xử lý POST từ client
router.post('/', (req, res) => {
    const { currentGPA, desiredGPA, currentCredits, totalCredits, remaining2CreditCourses, remaining3CreditCourses } = req.body;

    // Chuyển đổi dữ liệu về dạng số
    const currentGPA_float = parseFloat(currentGPA);
    const desiredGPA_float = parseFloat(desiredGPA);
    const currentCredits_int = parseInt(currentCredits);
    const totalCredits_int = parseInt(totalCredits);
    const remaining2CreditCourses_int = parseInt(remaining2CreditCourses);
    const remaining3CreditCourses_int = parseInt(remaining3CreditCourses);

    // Tính X
    let X;
    if (currentGPA_float === 0) {
        X = parseFloat(desiredGPA_float.toFixed(2)); // Nếu chưa có GPA, lấy trực tiếp desiredGPA
    } else {
        X = parseFloat((2 * desiredGPA_float - currentGPA_float).toFixed(2));  // Nếu có GPA, tính theo công thức
    }

    if (X < 0 || X > 4) {
        const maxGPA = parseFloat(((currentGPA_float + 4) / 2).toFixed(2)); // Làm tròn GPA tối đa
        const result = {
            GPA: maxGPA,
            message: `Không thể đạt được GPA mong muốn, GPA tối đa bạn có thể đạt được là ${maxGPA} khi tất cả các môn còn lại bạn đều đạt điểm A.`
        };
        console.log('Kết quả tính toán:', result);
        return res.json(result);
    }else{
        // Tìm Y và Z từ mảng điểm số
        const gradeScale = [1, 1.5, 2, 2.5, 3, 3.5, 4];
        let results = [];
    
        // Chạy vòng lặp để kiểm tra các giá trị từ gradeScale
        for (let Y of gradeScale) {
            for (let Z of gradeScale) {
    
                const numerator = (remaining2CreditCourses_int * 2 * Y) + (remaining3CreditCourses_int * 3 * Z);
                const denominator = (totalCredits_int - currentCredits_int);
    
                if (denominator === 0) continue; // Tránh chia cho 0
    
                const calculatedX = parseFloat((numerator / denominator).toFixed(2));
    
                // Kiểm tra điều kiện
                if (calculatedX > X - 0.01 && calculatedX <= 4) {
                    results.push({ Y: Y.toFixed(2), Z: Z.toFixed(2) });
                }
            }
        }
        
        //Kiểm tra kết quả trước khi nhận và trả về xem thử chính xác hay không
        console.log('Dữ liệu nhận từ client:', req.body);
        console.log('Kết quả tính toán:', results);
    
        // Trả kết quả dưới dạng JSON
        res.json(results);
    }
});

module.exports = router;
