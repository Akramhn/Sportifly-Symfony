window.onload = () =>{
    const FiltersForm = document.querySelector("#filters");

    //on boucle sur les input
    document.querySelectorAll("#filters input").forEach(input =>{
        input.addEventListener("change", () => {
            //récupertaion données de formuliare
            const Form = new FormData(FiltersForm);
            //on fabrique 'QueryString'
            const Params = new URLSearchParams();

            Form.forEach((value,key) =>{
                Params.append(key,value);

            });
            // on recupere l'url active

            const Url= new URL(window.location.href);
            console.log(Url)


            //requette Ajax

            fetch(Url.pathname + "?" + Params.toString() + "&ajax=1", {
                headers :{
                    "X-Requested-with" : "XMLHttpRequest"
                }

            }).then(response =>
                response.json()
            ).then(data => {
                   // On va chercher la zone de contenu
                  const content =  document.querySelector("#content");
                  // On remplace le contenu
                  content.innerHTML = data.content;
                }).catch(e => alert(e));

        });
    });
}