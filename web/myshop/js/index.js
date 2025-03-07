console.log('lets write script for cart')

var product_total_amt=document.getElementById('product_total_amt')
var shipping_charge=document.getElementById('shipping_charge')
var total_cart_amt=document.getElementById('total_cart_amt');
var  discount_code1=document.getElementById('discount_code1')
const decreaseNumber=(incdec,itemprice)=>{
    var itemval=document.getElementById(incdec);
    var itemprice=document.getElementById(itemprice);
    if(itemval.value<=0)
    {
        itemval.value=0;
        itemval.style.backgroundColor='red'
        itemprice.innerHTML=parseInt(itemprice.innerHTML)*0;
    }
    else{
        itemval.value=parseInt(itemval.value)-1;
        itemprice.innerHTML=parseInt(itemprice.innerHTML)-50;
        product_total_amt.innerHTML=parseInt(itemprice.innerHTML);
        total_cart_amt.innerHTML=parseInt(product_total_amt.innerHTML)+parseInt(shipping_charge.innerHTML)
    }

}
const increaseNumber=(incdec,itemprice)=>{
    var itemval=document.getElementById(incdec);
    var itemprice=document.getElementById(itemprice);
    if(itemval.value>=5)
    {
        itemval.value=5;
        alert('out of stock');
        itemprice.innerHTML=parseInt(itemprice.innerHTML)*0 +250;
    }
    else{
        itemval.value=parseInt(itemval.value)+1;
        itemval.style.backgroundColor='white'
        itemprice.innerHTML=parseInt(itemprice.innerHTML)+50;
        product_total_amt.innerHTML=parseInt(itemprice.innerHTML);
        total_cart_amt.innerHTML=parseInt(product_total_amt.innerHTML)+parseInt(shipping_charge.innerHTML)

    }
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-buy').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');

            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'product_id': productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Thành công',
                        text: 'Sản phẩm đã được thêm vào giỏ hàng',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Lỗi',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Lỗi',
                    text: 'Có lỗi xảy ra. Vui lòng thử lại.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            });
        });
    });

    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    
    if (message) {
        Swal.fire({
            title: 'Thông báo',
            text: message,
            icon: 'info',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-info'
            }
        });
    }
});