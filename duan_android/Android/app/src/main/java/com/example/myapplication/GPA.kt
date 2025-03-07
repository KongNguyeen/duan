package com.example.myapplication

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.SnackbarHost
import androidx.compose.material3.SnackbarHostState
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
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.navigation.NavHostController
import androidx.navigation.compose.rememberNavController
import com.example.myapplication.ui.theme.MyApplicationTheme
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import retrofit2.Response
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import retrofit2.http.Body
import retrofit2.http.POST

data class GPARequest(
    val currentGPA: String,
    val desiredGPA: String,
    val currentCredits: String,
    val totalCredits: String,
    val remaining2CreditCourses: String,
    val remaining3CreditCourses: String
)

data class Result(val Y: String, val Z: String)

interface ApiService {
    @POST("api/calculate")
    suspend fun calculateGPA(@Body gpaRequest: GPARequest): Response<List<Result>>
}

object RetrofitInstance {
    private val retrofit by lazy {
        Retrofit.Builder()
            .baseUrl("http://192.168.29.133:3000/")
            .addConverterFactory(GsonConverterFactory.create())
            .build()
    }

    val api: ApiService by lazy {
        retrofit.create(ApiService::class.java)
    }
}

class GPAA : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContent {
            MyApplicationTheme {
                val navController = rememberNavController()
                GPA(navController)
            }
        }
    }
}

@Composable
fun GPA(navController: NavHostController) {
    var currentGPA by remember { mutableStateOf("") }
    var desiredGPA by remember { mutableStateOf("") }
    var currentCredits by remember { mutableStateOf("") }
    var totalCredits by remember { mutableStateOf("") }
    var remainingSubjects2 by remember { mutableStateOf("") }
    var remainingSubjects3 by remember { mutableStateOf("") }
    var results by remember { mutableStateOf<List<Result>?>(null) }
    var message by remember { mutableStateOf("") }

    // Snackbar state
    val snackbarHostState = remember { SnackbarHostState() }

    Box(modifier = Modifier.fillMaxSize().background(Color.White)) {
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .height(46.dp)
                .background(Color(0, 134, 137))
        )

        Box(
            modifier = Modifier
                .fillMaxWidth()
                .height(24.dp)
                .align(Alignment.BottomCenter)
                .background(Color(0, 134, 137))
        )

        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(top = 46.dp)
                .padding(16.dp),
            verticalArrangement = Arrangement.Top,
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            item {
                Box(
                    modifier = Modifier
                        .width(350.dp)
                        .height(150.dp)
                        .align(Alignment.TopCenter)
                ) {
                    Image(
                        painter = painterResource(id = R.drawable.uth),
                        contentDescription = "",
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(150.dp)
                            .align(Alignment.TopCenter)
                    )
                }
            }

            item {
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(50.dp)
                        .background(Color(0, 134, 137)),
                    horizontalArrangement = Arrangement.Center,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(
                        text = "Tra cứu điểm sinh viên".uppercase(),
                        style = MaterialTheme.typography.titleMedium,
                        color = Color.White,
                        modifier = Modifier.clickable {
                            navController.popBackStack()
                        }
                    )
                    Spacer(modifier = Modifier.width(20.dp))
                    Text(
                        text = "Phân tích GPA".uppercase(),
                        style = MaterialTheme.typography.titleMedium,
                        color = Color.White,
                        modifier = Modifier.clickable {  }
                    )
                }
            }

            item {
                Spacer(modifier = Modifier.height(16.dp))

                OutlinedTextField(
                    value = currentGPA,
                    onValueChange = { currentGPA = it },
                    label = { Text("GPA hiện tại") },
                    modifier = Modifier.fillMaxWidth()
                )

                Spacer(modifier = Modifier.height(16.dp))

                OutlinedTextField(
                    value = desiredGPA,
                    onValueChange = { desiredGPA = it },
                    label = { Text("GPA mong muốn") },
                    modifier = Modifier.fillMaxWidth()
                )

                Spacer(modifier = Modifier.height(16.dp))

                OutlinedTextField(
                    value = currentCredits,
                    onValueChange = { currentCredits = it },
                    label = { Text("Tín chỉ hiện tại") },
                    modifier = Modifier.fillMaxWidth()
                )

                Spacer(modifier = Modifier.height(16.dp))

                OutlinedTextField(
                    value = totalCredits,
                    onValueChange = { totalCredits = it },
                    label = { Text("Tín chỉ cần hoàn thành") },
                    modifier = Modifier.fillMaxWidth()
                )

                Spacer(modifier = Modifier.height(16.dp))

                OutlinedTextField(
                    value = remainingSubjects2,
                    onValueChange = { remainingSubjects2 = it },
                    label = { Text("Số môn 2 tín chỉ còn lại") },
                    modifier = Modifier.fillMaxWidth()
                )

                Spacer(modifier = Modifier.height(16.dp))

                OutlinedTextField(
                    value = remainingSubjects3,
                    onValueChange = { remainingSubjects3 = it },
                    label = { Text("Số môn 3 tín chỉ còn lại") },
                    modifier = Modifier.fillMaxWidth()
                )

                Spacer(modifier = Modifier.height(16.dp))

                Button(
                    onClick = {
                        CoroutineScope(Dispatchers.IO).launch {
                            val response = RetrofitInstance.api.calculateGPA(
                                GPARequest(
                                    currentGPA,
                                    desiredGPA,
                                    currentCredits,
                                    totalCredits,
                                    remainingSubjects2,
                                    remainingSubjects3
                                )
                            )
                            if (response.isSuccessful) {
                                results = response.body()
                                val builder = StringBuilder()
                                builder.append("Kết quả Tính Toán GPA\n")
                                builder.append("Dựa trên thông tin bạn đã cung cấp, chúng tôi đã tính toán được số điểm cần đạt cho các môn học còn lại của bạn. Để đạt được GPA mong muốn, bạn cần chú ý đến các môn sau:\n\n")

                                results?.forEachIndexed { index, result ->
                                    val yValue = result.Y
                                    val zValue = result.Z
                                    builder.append(" - 2 tín chỉ: $yValue\n")
                                    builder.append(" - 3 tín chỉ: $zValue\n\n")
                                }

                                builder.append("Chúc bạn thành công trong việc hoàn thành các môn học! Hãy nỗ lực hết mình để đạt được mục tiêu GPA mà bạn đã đề ra. Nếu bạn cần thêm sự hỗ trợ hay có câu hỏi gì, đừng ngần ngại liên hệ với chúng tôi.\n\n")
                                builder.append("Cảm ơn bạn đã tin tưởng và lựa chọn chúng tôi. Chúc bạn một ngày thật tuyệt vời!")

                                message = builder.toString()
                            }
                        }
                    },
                    modifier = Modifier.fillMaxWidth(),
                    colors = ButtonDefaults.buttonColors(containerColor = Color(0, 134, 137)),
                    shape = RectangleShape
                ) {
                    Text("Phân tích", color = Color.White)
                }

                // Display results message if available
                if (message.isNotEmpty()) {
                    Spacer(modifier = Modifier.height(16.dp))
                    Text(text = message)
                }
            }
        }
    }

    SnackbarHost(hostState = snackbarHostState)
}

@Preview(showBackground = true)
@Composable
fun PreviewGPA() {
    MyApplicationTheme {
        GPA(navController = rememberNavController())
    }
}
