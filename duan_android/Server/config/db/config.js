const { Pool } = require('pg');

// Tạo kết nối tới PostgreSQL
const pool = new Pool({
  user: 'postgres',
  host: 'localhost',
  database: 'DiemSV',
  password: 'huytoan0801',
  port: 5432,
});

// Hàm async để kết nối với PostgreSQL
async function connect() {
  try {
    const client = await pool.connect();
    console.log('Connection to database Successful');
    client.release();  // Giải phóng client sau khi kết nối thành công
  } catch (error) {
    console.error('Error acquiring client', error.stack);
  }
}

module.exports = { connect, pool };
