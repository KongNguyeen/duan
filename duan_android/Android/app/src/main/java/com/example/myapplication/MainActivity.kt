package com.example.myapplication

import android.os.Bundle
import android.util.Log
import android.widget.Toast
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.DropdownMenuItem
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.ExposedDropdownMenuBox
import androidx.compose.material3.ExposedDropdownMenuDefaults
import androidx.compose.material3.HorizontalDivider
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.RectangleShape
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.TextStyle
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.navigation.NavHostController
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import com.example.myapplication.ui.theme.MyApplicationTheme
import okhttp3.Call
import okhttp3.Callback
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.Response
import org.json.JSONArray
import org.json.JSONException
import java.io.IOException


data class StudentInfo(
    val fullName: String,
    val studentId: String,
    val className: String,
    val department: String,
    val grades: List<GradeInfo>
)

data class GradeInfo(
    val subjectId: String,
    val subjectName: String,
    val grade: String
)

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContent {
            MyApplicationTheme {
                val navController = rememberNavController()
                NavHost(navController = navController, startDestination = "gradeChecker") {
                    composable("gradeChecker") {
                        Scaffold(
                            modifier = Modifier.fillMaxSize(),
                            containerColor = Color(0, 134, 137)
                        ) { innerPadding ->
                            Surface(
                                modifier = Modifier.fillMaxSize().padding(innerPadding),
                                color = Color.White
                            ) {
                                GradeCheckerDisplay(navController)
                            }
                        }
                    }
                    composable("gpa") { GPA(navController) }
                    composable("bangDiem") { BangDiem() }
                }
            }
        }
    }
}

@Preview
@Composable
fun MainBangDiemPreview() {
    BangDiem()
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun GradeCheckerDisplay(navController: NavHostController, modifier: Modifier = Modifier) {
    var studentId by remember { mutableStateOf("") }
    var studentInfo by remember { mutableStateOf<StudentInfo?>(null) }
    var expanded by remember { mutableStateOf(false) }
    var selectedSemester by remember { mutableStateOf("Học kỳ 1 năm học 2022-2023") }

    val semesters = listOf(
        "Học kỳ 1 năm học 2022-2023",
        "Học kỳ 2 năm học 2022-2023",
        "Học kỳ 1 năm học 2023-2024",
        "Học kỳ 2 năm học 2023-2024",
        "Học kỳ 1 năm học 2024-2025",
        "Học kỳ 2 năm học 2024-2025"
    )

    val context = LocalContext.current
    var toastMessage by remember { mutableStateOf("") }

    LazyColumn(
        modifier = modifier.fillMaxSize().padding(16.dp),
        verticalArrangement = Arrangement.Top,
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        item {
            Box(
                modifier = Modifier.width(350.dp).height(150.dp)
            ) {
                Image(
                    painter = painterResource(id = R.drawable.uth),
                    contentDescription = "",
                    modifier = Modifier.fillMaxWidth().height(150.dp).align(Alignment.TopCenter)
                )
            }
        }

        item {
            Row(
                modifier = Modifier.fillMaxWidth().height(50.dp).background(Color(0, 134, 137)),
                horizontalArrangement = Arrangement.Center,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = "Tra cứu điểm sinh viên".uppercase(),
                    style = MaterialTheme.typography.titleMedium,
                    color = Color.White,
                    modifier = Modifier.clickable { }
                )
                Spacer(modifier = Modifier.width(20.dp))
                Text(
                    text = "Phân tích GPA".uppercase(),
                    style = MaterialTheme.typography.titleMedium,
                    color = Color.White,
                    modifier = Modifier.clickable { navController.navigate("gpa") }
                )
            }
        }

        item {
            Spacer(modifier = Modifier.height(16.dp))

            OutlinedTextField(
                value = studentId,
                onValueChange = { studentId = it },
                label = { Text(
                    text = "Mã số sinh viên",
                    color = Color.Black
                ) },
                textStyle = TextStyle(
                    color = Color.Black,
                ),
                modifier = Modifier.fillMaxWidth()
            )

            Spacer(modifier = Modifier.height(16.dp))

            ExposedDropdownMenuBox(
                expanded = expanded,
                onExpandedChange = { expanded = !expanded },
                modifier = Modifier.fillMaxWidth()
            ) {
                OutlinedTextField(
                    readOnly = true,
                    value = selectedSemester,
                    onValueChange = { },
                    label = { Text("Lựa chọn học kỳ")},
                    trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = expanded) },
                    textStyle = TextStyle(color = Color.Black),
                    modifier = Modifier.menuAnchor().fillMaxWidth().background(Color.White)
                )
                ExposedDropdownMenu(
                    expanded = expanded,
                    onDismissRequest = { expanded = false }
                ) {
                    semesters.forEach { semester ->
                        DropdownMenuItem(
                            text = { Text(
                                text = semester,
                                color = Color.Black
                            ) },
                            onClick = {
                                selectedSemester = semester
                                expanded = false
                            },
                            modifier = Modifier.background(if (semester == selectedSemester) Color.LightGray else Color.White)
                        )
                    }
                }
            }

            Spacer(modifier = Modifier.height(16.dp))

            Button(
                onClick = {
                    val classYear = when (selectedSemester) {
                        "Học kỳ 1 năm học 2022-2023" -> "2022_hocky1"
                        "Học kỳ 2 năm học 2022-2023" -> "2022_hocky2"
                        "Học kỳ 1 năm học 2023-2024" -> "2023_hocky1"
                        "Học kỳ 2 năm học 2023-2024" -> "2023_hocky2"
                        "Học kỳ 1 năm học 2024-2025" -> "2024_hocky1"
                        "Học kỳ 2 năm học 2024-2025" -> "2024_hocky2"
                        else -> "unknown"
                    }
                    Toast.makeText(context, "Đang lấy dữ liệu...", Toast.LENGTH_LONG).show()
                    getStudentInfoFromServer(studentId, classYear) { info ->
                        studentInfo = info
                        if (studentInfo == null) {
                            toastMessage = "Không tìm thấy thông tin sinh viên!"
                        }
                    }
                },
                modifier = Modifier.fillMaxWidth(),
                colors = ButtonDefaults.buttonColors(containerColor = Color(0, 134, 137)),
                shape = RectangleShape
            ) {
                Text(
                    text = "Tra cứu",
                    color = Color.White
                )
            }

            if (toastMessage.isNotEmpty()) {
                Toast.makeText(context, toastMessage, Toast.LENGTH_SHORT).show()
                toastMessage = ""
            }
        }

        studentInfo?.let {
            item {
                Column(horizontalAlignment = Alignment.Start) {
                    Text(text = "Họ và Tên: ${it.fullName}", style = MaterialTheme.typography.bodyLarge)
                    Spacer(modifier = Modifier.height(10.dp))
                    Text(text = "Mã số sinh viên: ${it.studentId}", style = MaterialTheme.typography.bodyLarge)
                    Spacer(modifier = Modifier.height(10.dp))
                    Text(text = "Lớp: ${it.className}", style = MaterialTheme.typography.bodyLarge)
                    Spacer(modifier = Modifier.height(10.dp))
                    Text(text = "Khoa: ${it.department}", style = MaterialTheme.typography.bodyLarge)
                    Spacer(modifier = Modifier.height(10.dp))
                    Text(text = "Học kỳ: $selectedSemester", style = MaterialTheme.typography.bodyLarge)
                    Spacer(modifier = Modifier.height(20.dp))

                    // Table header
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .background(Color(0, 134, 137))
                    ) {
                        Text("Mã môn học", modifier = Modifier.weight(2f), color = Color.White)
                        Text("Tên môn học", modifier = Modifier.weight(2f), color = Color.White)
                        Text("Điểm", modifier = Modifier.weight(1f), color = Color.White, textAlign = TextAlign.End)
                    }

                    // Display grades in a LazyColumn
                    this@LazyColumn.items(it.grades) { grade ->
                        Row(modifier = Modifier.fillMaxWidth()) {
                            Text(grade.subjectId, modifier = Modifier.weight(2f))
                            Text(grade.subjectName, modifier = Modifier.weight(2f))
                            Text(grade.grade, modifier = Modifier.weight(1f), textAlign = TextAlign.End)
                        }
                        HorizontalDivider(
                            modifier = Modifier.padding(vertical = 4.dp),
                            color = Color.LightGray
                        )
                    }
                }
            }
        } ?: run {
            item {
                if (studentId.isNotEmpty()) {
                    Text(text = "Sinh viên không tồn tại", style = MaterialTheme.typography.bodyLarge)
                }
            }
        }
    }
}


private fun getStudentInfoFromServer(studentId: String, classYear: String, onResult: (StudentInfo?) -> Unit) {
    val client = OkHttpClient()
    val url = "http://192.168.29.133:3000/api/getstudent/$studentId/$classYear"
    val request = Request.Builder()
        .url(url)
        .build()

    client.newCall(request).enqueue(object : Callback {
        override fun onFailure(call: Call, e: IOException) {
            Log.e("NetworkError", "Failed to fetch student info: ${e.message}")
            onResult(null)
        }

        override fun onResponse(call: Call, response: Response) {
            response.use {
                if (!it.isSuccessful) {
                    Log.e("NetworkError", "Unexpected code: ${it.code}")
                    onResult(null)
                    return
                }

                val jsonData = it.body?.string()
                Log.d("ResponseData", "Received student info: $jsonData")
                val studentInfo = parseStudentInfoFromJson(jsonData)
                onResult(studentInfo)
            }
        }
    })
}

private fun parseStudentInfoFromJson(jsonData: String?): StudentInfo? {
    return try {
        if (jsonData.isNullOrEmpty()) return null

        val jsonArray = JSONArray(jsonData)
        if (jsonArray.length() == 0) return null

        val jsonObject = jsonArray.getJSONObject(0)
        val fullName = jsonObject.getString("student_name")
        val studentId = jsonObject.getString("studentid")
        val className = jsonObject.getString("studentclass")
        val department = jsonObject.getString("department")

        val grades = mutableListOf<GradeInfo>()
        for (i in 0 until jsonArray.length()) {
            val gradeObject = jsonArray.getJSONObject(i)
            grades.add(
                GradeInfo(
                    subjectId = gradeObject.getString("subjectid"),
                    subjectName = gradeObject.getString("subjectname"),
                    grade = gradeObject.getString("grades")
                )
            )
        }

        StudentInfo(fullName, studentId, className, department, grades)
    } catch (e: JSONException) {
        Log.e("JSONError", "Error parsing JSON: ${e.message}")
        null
    }
}
