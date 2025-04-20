package com.example.myapplication.model

data class Post(
    val id: String,
    val ten: String,
    val content: String,
    val media_url: String?,
    val created_at: String,
    val likes: String,
    val comments: String,
    val shares: String
)
