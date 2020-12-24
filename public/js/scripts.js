window.addEventListener('load', function(){

// window.onLoad = () => {
    let activate = document.querySelectorAll("[type=checkbox]");
    for(let button of activate){
        button.addEventListener("click", function(){
            let xmlhttp = new XMLHttpRequest();

            // xmlhttp.onReadyStateChange = () => {}

            xmlhttp.open("get", `/admin/announces/activate/${this.dataset.id}`);
            xmlhttp.send();
        })
    }

    let deleteAnnounce = document.querySelectorAll(".modal-trigger");
    for(let button of deleteAnnounce){
        button.addEventListener("click", function(){

            document.querySelector(".modal-content").innerText = `Do you really want to delete "${this.dataset.title}" ?`;
            document.querySelector(".modal-footer a").href=`/admin/announces/delete/${this.dataset.id}`;


        })
    }

})