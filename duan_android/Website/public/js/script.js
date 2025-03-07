function showGradeLookup() {
  document.getElementById('tradiemsinhvien').style.display = 'block'; // Hiện form tra cứu điểm sinh viên
  document.getElementById('tinhdiemsinhvien').style.display = 'none'; // Ẩn form tính điểm
  document.getElementById('studentInfo').style.display = 'none'; // Ẩn thông tin sinh viên
  document.getElementById('resultTable').style.display = 'none'; // Ẩn bảng kết quả
  document.getElementById('ketqua').style.display = 'none'; // Ẩn bảng kết quả
  document.getElementById('gradeLookupForm').reset();
  const imageColumn = document.getElementById('imageColumn');
  imageColumn.innerHTML = ''; 
  document.getElementById('result').innerHTML = '';
}

function showGpaCalculator() {
  document.getElementById('tradiemsinhvien').style.display = 'none'; // Ẩn form tra cứu điểm sinh viên
  document.getElementById('tinhdiemsinhvien').style.display = 'block'; // Hiện form tính điểm
  document.getElementById('studentInfo').style.display = 'none'; // Ẩn thông tin sinh viên
  document.getElementById('resultTable').style.display = 'none'; // Ẩn bảng kết quả
  document.getElementById('ketqua').style.display = 'none'; 
  document.getElementById('gpaForm').reset();
  const imageColumn = document.getElementById('imageColumn');
  imageColumn.innerHTML = ''; 
  document.getElementById('result').innerHTML = '';
}

async function getStudentGrade() {
  const mssv = document.getElementById('mssv').value; // Lấy MSSV từ form input
  const classYear = document.getElementById('classYear').value; // Lấy học kỳ từ form input

  const request = new Request(`http://192.168.1.10:3000/api/getstudent/${mssv}/${classYear}`, {
    method: 'GET'
  });

  try {
    const response = await fetch(request); // Gửi request tới API
    const data = await response.json(); // Chuyển kết quả sang dạng JSON

    if (response.ok && data.length > 0) {
      // Populate student information fields from the first row
      document.getElementById('studentName').textContent = data[0].student_name;
      document.getElementById('studentid').textContent = data[0].studentid;
      document.getElementById('studentclass').textContent = data[0].studentclass;
      document.getElementById('department').textContent = data[0].department;
      document.getElementById('studentInfo').style.display = 'block';

      // Populate the grades table
      const resultTable = document.getElementById('resultTable');
      const resultBody = document.getElementById('resultBody');

      // Clear previous table data
      resultBody.innerHTML = '';

      // Loop through grades and display them
      data.forEach((item) => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${item.subjectid}</td>
          <td>${item.subjectname}</td>
          <td>${item.grades}</td>
        `;
        resultBody.appendChild(row);
      });

      // Show the student info section and result table
      document.getElementById('resultTable').style.display = 'block';
    } else {
      alert('Không tìm thấy sinh viên hoặc môn học');
    }
  } catch (error) {
    console.error('Error fetching student grade:', error); // Xử lý lỗi
    alert('Lỗi khi lấy dữ liệu');
  }
}


function calculateGPA() {
  // Lấy dữ liệu từ form
  const currentGPA = document.getElementById('currentGPA').value;
  const desiredGPA = document.getElementById('desiredGPA').value;
  const currentCredits = document.getElementById('currentCredits').value;
  const totalCredits = document.getElementById('totalCredits').value;
  const remaining2CreditCourses = document.getElementById('remaining2CreditCourses').value;
  const remaining3CreditCourses = document.getElementById('remaining3CreditCourses').value;
  
  // Tính tổng tín chỉ từ số môn 2 tín chỉ và 3 tín chỉ
  const totalCreditsFromCourses = (remaining2CreditCourses * 2) + (remaining3CreditCourses * 3);

  // Tính số tín chỉ cần hoàn thành
  const creditsToComplete = totalCredits - currentCredits;

  // Kiểm tra điều kiện hợp lệ
  if (totalCreditsFromCourses !== creditsToComplete) {
    alert(`Tổng tín chỉ từ số môn 2 tín chỉ và 3 tín chỉ phải bằng ${creditsToComplete}. Vui lòng kiểm tra lại!`);
    return; // Dừng lại nếu dữ liệu không hợp lệ
  }
  document.getElementById('ketqua').style.display = 'flex';
 
  // Tạo đối tượng dữ liệu gửi đi
  const data = {
      currentGPA: currentGPA,
      desiredGPA: desiredGPA,
      currentCredits: currentCredits,
      totalCredits: totalCredits,
      remaining2CreditCourses: remaining2CreditCourses,
      remaining3CreditCourses: remaining3CreditCourses
  };

  // Gửi yêu cầu fetch tới server
  fetch('http://192.168.1.10:3000/api/calculate', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
      },
      body: JSON.stringify(data) // Gửi dữ liệu dưới dạng JSON
  })
  .then(response => {
      if (!response.ok) {
          throw new Error('Network response was not ok');
      }
      return response.json();
  })
  .then(result => {
      let resultHTML = 'Chúng tôi tính điểm của bạn khoảng từ GPA mong muốn đến nhỏ hơn 4.0 nha, ai mà chẳng muốn mình giỏi hơn đúng không nè:)))\n\n';
      if (result.message) {
        resultHTML += result.message; // Hiển thị thông báo không thể đạt được GPA mong muốn
      }else {
        resultHTML += 'Số điểm các môn 2 tín chỉ và 3 tín chỉ bạn cần đạt để đúng theo mong muốn của bạn là:\n';
        result.forEach(pair => {
          resultHTML += ` 
          Môn 2 chỉ : ${pair.Y}
          Môn 3 chỉ : ${pair.Z}.\n`;
        });
      }
      resultHTML +=`\nCảm ơn bạn đã quan tâm đến chúng tôi, chúc bạn một ngày tốt lành!!!`
      displayTypingEffect(resultHTML, 'result', 50); // Hiển thị kết quả
  })
  .catch(error => {
      console.error('Error:', error);
      document.getElementById('result').innerHTML = '<p>Có lỗi xảy ra khi tính toán.</p>';
  });
}

function displayTypingEffect(text, elementId, delay) {
  const element = document.getElementById(elementId);
  element.innerHTML = ''; // Xóa nội dung hiện tại

  const lines = text.split('\n'); // Tách văn bản thành các dòng
  let currentLine = 0; // Biến theo dõi dòng hiện tại

  function typeLine() {
      if (currentLine < lines.length) {
          const line = lines[currentLine];
          let index = 0; // Biến theo dõi ký tự hiện tại của dòng

          function type() {
              if (index < line.length) {
                  element.innerHTML += line.charAt(index); // Thêm từng ký tự vào phần tử
                  index++;
                  setTimeout(type, delay); // Gọi lại hàm sau khoảng thời gian delay
              } else {
                  // Sau khi hoàn thành dòng, thêm xuống dòng
                  element.innerHTML += '<br>'; // Thêm thẻ <br> để xuống dòng
                  currentLine++; // Chuyển sang dòng tiếp theo
                  setTimeout(typeLine, delay * 2); // Delay trước khi bắt đầu dòng mới
              }
          }

          type(); // Bắt đầu gõ chữ cho dòng hiện tại
      }else{
        showImage();
      }
  }

  typeLine(); // Bắt đầu hiệu ứng gõ chữ cho toàn bộ văn bản
}


function showImage() {
  const imageColumn = document.getElementById('imageColumn');
  const image = document.createElement('img');
  image.src = './public/img/quydoi.jpg'; // Đường dẫn tới hình ảnh bắt đầu từ server đó
  image.alt = 'Kết quả hình ảnh';
  image.style.width = '100%'; 
  image.style.height = '100%';
  imageColumn.appendChild(image); // Thêm hình ảnh vào cột bên phải
}


