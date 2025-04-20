package com.example.myapplication.ui.screen

import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.padding
import androidx.compose.material.*
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.Person
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.unit.dp
import com.example.myapplication.model.Post
import com.example.myapplication.ui.component.PostItem

@Composable
fun MainScreen(posts: List<Post>) {
    var selectedItem by remember { mutableStateOf(0) }

    Scaffold(
        bottomBar = {
            BottomNavigation(
                backgroundColor = Color.White,
                contentColor = Color.Black
            ) {
                BottomNavigationItem(
                    icon = { Icon(Icons.Default.Home, contentDescription = "Home") },
                    label = { Text("Home") },
                    selected = selectedItem == 0,
                    onClick = { selectedItem = 0 }
                )
                BottomNavigationItem(
                    icon = { Icon(Icons.Default.Person, contentDescription = "Profile") },
                    label = { Text("Profile") },
                    selected = selectedItem == 1,
                    onClick = { selectedItem = 1 }
                )
            }
        }
    ) { innerPadding ->
        when (selectedItem) {
            0 -> Column(modifier = Modifier.padding(innerPadding)) {
                posts.forEach { post ->
                    PostItem(post = post, onLikeClicked = { /* handle like */ })
                }
            }

            1 -> Column(modifier = Modifier.padding(innerPadding)) {
                Text("Profile screen content here", modifier = Modifier.padding(16.dp))
            }
        }
    }
}
