window.onload = ()=>{
 Stripe = Stripe('pk_test_51LpWzeK8zI5Hqeq06iLuwboMxLC1Gji2qGDeGYW18OsfLgOh39962BIOyxHv3V2FYgoJvqofqhI2szkNg8HPU6cV00qUovUR9A');
    let elements = Stripe.elements();
    let redirect = 'home?valid=ok';
    let name = document.getElementById('name');
    let button = document.getElementById('valid');
    let clientSecret = button.dataset.secret;

    let card = elements.create('card');
    card.mount('#card-part');

    card.addEventListener("change" , (event)=> {
        let displayErrors = document.getElementById('carderror');
        if (event.error) {
            displayErrors.textContent = event.error.message
        }else {
            displayErrors.textContent = "";
        }
    })

    button.addEventListener('click' , (event) => {
        Stripe.handleCardPayment(
            clientSecret , card ,{
                payment_method_data: {
                    billing_details : {
                        name: name.value
                    }
                }
            }
        ).then((result)=>{
            if (result.error) {
                document.getElementById('errors').innerText = result.error.message
            }else{
                document.location.href = redirect ;
            }
        })
    })
}