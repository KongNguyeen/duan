package com.example.myapplication

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp


@Composable
fun BangDiem() {
    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF008689))
        ,
        contentAlignment = Alignment.Center
    ) {
        Image(
            painter = painterResource(id = R.drawable.bangdiem),
            contentDescription = "Bảng quy đổi điểm",
            modifier = Modifier
                .fillMaxWidth()
                .height(500.dp)
        )
    }
}

@Preview
@Composable
fun BangDiemPreview() {
    BangDiem()
}
