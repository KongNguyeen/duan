package com.example.myapplication

import android.os.Bundle
import android.widget.Toast
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.*
import androidx.compose.material.*
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.Person
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import com.example.myapplication.ui.screen.PostListScreen
import com.example.myapplication.ui.theme.MyApplicationTheme

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContent {
            MainApp()
        }
    }
}

@Composable
fun MainApp() {
    MyApplicationTheme {
        Surface(color = MaterialTheme.colors.background) {
            var selectedTab by remember { mutableStateOf(0) }

            Scaffold(
                // 👉 Thêm thanh TopAppBar ở đây
                topBar = {
                    TopAppBar(
                        title = { Text("Bản tin") },
                        backgroundColor = Color(0xFF1976D2), // Màu xanh dương đậm
                        contentColor = Color.White
                    )
                },
                bottomBar = {
                    BottomNavigation(
                        backgroundColor = Color(0xFFBBDEFB), // Màu xanh dương nhạt
                        contentColor = Color.Black
                    ) {
                        BottomNavigationItem(
                            icon = { Icon(Icons.Default.Home, contentDescription = "Home") },
                            label = { Text("Home") },
                            selected = selectedTab == 0,
                            onClick = { selectedTab = 0 }
                        )
                        BottomNavigationItem(
                            icon = { Icon(Icons.Default.Person, contentDescription = "Profile") },
                            label = { Text("Profile") },
                            selected = selectedTab == 1,
                            onClick = { selectedTab = 1 }
                        )
                    }
                }
            ) { paddingValues ->
                Box(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(paddingValues)
                ) {
                    when (selectedTab) {
                        0 -> PostListScreen()
                        1 -> ProfileScreen()
                    }
                }
            }
        }
    }
}

@Composable
fun ProfileScreen() {
    val context = LocalContext.current  // lấy context cho Intent

    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(16.dp),
        verticalArrangement = Arrangement.Center,
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        Icon(
            imageVector = Icons.Default.Person,
            contentDescription = "Profile",
            modifier = Modifier.size(80.dp),
            tint = Color.Gray
        )
        Spacer(modifier = Modifier.height(12.dp))
        Text(
            text = "Xin chào, User!",
            style = MaterialTheme.typography.h6
        )
        Spacer(modifier = Modifier.height(20.dp))

        Button(onClick = {
            val intent = android.content.Intent(context, LoginActivity::class.java)
            context.startActivity(intent)
        }) {
            Text("Đăng nhập")
        }

        Spacer(modifier = Modifier.height(12.dp))

        // Thêm nút Đăng ký
        Button(onClick = {
            val intent = android.content.Intent(context, RegisterActivity::class.java)
            context.startActivity(intent)
        }) {
            Text("Đăng ký")
        }
    }
}


@Preview(showBackground = true, widthDp = 360, heightDp = 640)
@Composable
fun MainAppPreview() {
    MainApp()
}
