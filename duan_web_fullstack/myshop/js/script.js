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
 const discountCode =()=>{
        console.log('COUPAN CODE BUTTON')
        var  error_trw=document.getElementById('error_trw')
        let totalAmtCurrent=parseInt(total_cart_amt.innerHTML)
        if(discount_code1 =='himansh3198')
        {
            let newtotal=totalAmtCurrent-20;
        total_cart_amt.innerHTML=newtotal;
   
        error_trw.innerHTML="COUPAN IS APPLIED SUCCESSFULLY !!!"
        }
        else{
           
            
            error_trw.innerHTML="OOPS COUPAN IS INVALID  !!!"
        }
        

       }
}
