window.onload = ()=>{
 Stripe = Stripe('pk_live_51LydyME5OayV6Hmp1ed0NKzhYrTrzP4CyMxjiLF9IPNWO6c2w4KHyshTEHiXElrrhGheNp3dppZarVChQ2uqYWB100yYmujAk2');
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


