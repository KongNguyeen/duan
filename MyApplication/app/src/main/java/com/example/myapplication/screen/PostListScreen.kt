package com.example.myapplication.ui.screen

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import com.example.myapplication.model.Post
import com.example.myapplication.network.RetrofitClient
import com.example.myapplication.ui.component.PostItem
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

@Composable
fun PostListScreen() {
    var postList by remember { mutableStateOf<List<Post>>(emptyList()) }
    var isLoading by remember { mutableStateOf(true) }
    var errorMessage by remember { mutableStateOf<String?>(null) }
    var likedPosts by remember { mutableStateOf(setOf<String>()) }

    LaunchedEffect(Unit) {
        RetrofitClient.instance.getPosts().enqueue(object : Callback<List<Post>> {
            override fun onResponse(call: Call<List<Post>>, response: Response<List<Post>>) {
                isLoading = false
                if (response.isSuccessful) {
                    postList = response.body()?.sortedBy { it.id.toInt() } ?: emptyList()
                } else {
                    errorMessage = "Lỗi máy chủ: ${response.code()}"
                }
            }

            override fun onFailure(call: Call<List<Post>>, t: Throwable) {
                isLoading = false
                errorMessage = "Không thể kết nối: ${t.message}"
            }
        })
    }

    val toggleLike: (Post) -> Unit = { post ->
        likedPosts = if (likedPosts.contains(post.id)) {
            likedPosts - post.id
        } else {
            likedPosts + post.id
        }

        postList = postList.map {
            if (it.id == post.id) {
                val currentLikes = it.likes.toIntOrNull() ?: 0
                val newLikes = if (likedPosts.contains(post.id)) currentLikes + 1 else currentLikes - 1
                it.copy(likes = newLikes.coerceAtLeast(0).toString())
            } else it
        }
    }

    when {
        isLoading -> {
            Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator()
            }
        }

        errorMessage != null -> {
            Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                Text(
                    text = errorMessage ?: "Lỗi không xác định",
                    color = MaterialTheme.colorScheme.error
                )
            }
        }

        else -> {
            LazyColumn(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(8.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                items(postList) { post ->
                    PostItem(
                        post = post,
                        onLikeClicked = { toggleLike(it) }
                    )
                }
            }
        }
    }
}


@Preview(showBackground = true)
@Composable
fun PostListScreenPreview() {
    val fakePosts = listOf(
        Post(
            id = "1",
            ten = "1",
            content = "Hôm nay trời đẹp quá 🌞",
            media_url = "https://example.com/image1.jpg",
            created_at = "2025-04-20 11:59:50",
            likes = "120",
            comments = "45",
            shares = "30"
        ),
        Post(
            id = "2",
            ten = "2",
            content = "Cà phê sáng chill ☕",
            media_url = null,
            created_at = "2025-04-21 08:15:00",
            likes = "95",
            comments = "18",
            shares = "7"
        )
    )

    MaterialTheme {
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(8.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            items(fakePosts) { post ->
                PostItem(
                    post = post,
                    onLikeClicked = {}
                )
            }
        }
    }
}

