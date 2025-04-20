package com.example.myapplication.network

import com.example.myapplication.model.Post
import retrofit2.Call
import retrofit2.http.GET

interface ApiService {
    @GET("posts.php")
    fun getPosts(): Call<List<Post>>
}
