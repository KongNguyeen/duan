package com.example.myapplication

import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.EditText
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.android.volley.toolbox.StringRequest
import com.android.volley.toolbox.Volley
import android.content.Intent
import com.example.myapplication.R

class LoginActivity : AppCompatActivity() {

    private lateinit var usernameEditText: EditText
    private lateinit var passwordEditText: EditText
    private lateinit var loginButton: Button
    private lateinit var errorText: TextView

    private val url = "http://10.0.2.2/api/login.php"

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_login)  // Ensure the ayout file is correctly named

        usernameEditText = findViewById(R.id.editTextUsername)
        passwordEditText = findViewById(R.id.editTextPassword)
        loginButton = findViewById(R.id.buttonLogin)
        errorText = findViewById(R.id.errorText)

        loginButton.setOnClickListener {
            loginUser()
        }
    }

    private fun loginUser() {
        val username = usernameEditText.text.toString().trim()
        val password = passwordEditText.text.toString().trim()

        // Kiểm tra xem các trường có rỗng không
        if (username.isEmpty() || password.isEmpty()) {
            errorText.visibility = View.VISIBLE
            errorText.text = "Vui lòng nhập đầy đủ tài khoản và mật khẩu!"
            return
        }

        // Tạo yêu cầu đăng nhập
        val stringRequest = object : StringRequest(Method.POST, url,
            { response ->
                when (response) {
                    "success" -> Toast.makeText(this, "Đăng nhập thành công!", Toast.LENGTH_SHORT).show()
                    "invalid" -> Toast.makeText(this, "Sai mật khẩu!", Toast.LENGTH_SHORT).show()
                    "not_found" -> Toast.makeText(this, "Tài khoản không tồn tại!", Toast.LENGTH_SHORT).show()
                    else -> Toast.makeText(this, "Lỗi server!", Toast.LENGTH_SHORT).show()
                }
            },
            { error ->
                Toast.makeText(this, "Lỗi kết nối: ${error.message}", Toast.LENGTH_SHORT).show()
            }) {
            override fun getParams(): Map<String, String> {
                return mapOf(
                    "username" to username,
                    "password" to password
                )
            }
        }

        // Thêm yêu cầu vào hàng đợi của Volley
        val requestQueue = Volley.newRequestQueue(this)
        requestQueue.add(stringRequest)
    }
}
