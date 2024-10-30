const carrito = document.getElementById("carrito");
const elementos1 = document.getElementById("lista-1");


const lista = document.querySelector("#lista-carrito tbody");
const vaciarcarritobtn = document.getElementById("vaciar-carrito");

cargarEventListener();

function cargarEventListener() {

    elementos1.addEventListener("click" , comprarElemento);
    carrito.addEventListener("click", eliminarElemento);
    vaciarcarritobtn.addEventListener("click", vaciarCarrito);


}

function comprarElemento(e) {
    e.preventDefault();
    if(e.target.classList.contains("agregar-carrito")) {
        const elemento = e.target.parentElement.parentElement;
        leerDatosElemento(elemento);
    }


}

function leerDatosElemento(elemento) {
    const infoelemento = {
        imagen: elemento.querySelector("img").src,
        titulo: elemento.querySelector("h3").textContent,
        precio: elemento.querySelector(".precio").textContent,
        id: elemento.querySelector("a").getAttribute("data-id")

    }
    insertarCarrito(infoelemento);
}

function insertarCarrito(elemento) {

    const row = document.createElement("tr");
    row.innerHTML = `
        <td>
            <img src="${elemento.imagen}" width=100 />
        </td>
        <td>
            ${elemento.titulo}
        </td>

        <td>
            ${elemento.precio}
        </td>

        <td>
             <a href="a" class="borrar" data-id="${elemento.id}">x </a>
         </td>
     `;

    lista.appendChild(row);
}  

function eliminarElemento(e) {
    e.preventDefault();
    let elemento,
        elementoid;
    if(e.target.classList.contains("borrar")) {
        e.target.parentElement.parentElement.remove();
        elemento = e.target.parentElement.parentElement;
        elementoid = elemento.querySelector("a").getAttribute("data-id");
    }
}

function vaciarCarrito () {
    while(lista.firstChild) {
        lista.removeChild(lista.firstChild);
    }
    return false;
}
document.addEventListener("DOMContentLoaded", function() {
    const addToCartButtons = document.querySelectorAll(".btn-agregar");

    addToCartButtons.forEach(button => {
        button.addEventListener("click", function() {
            const productId = this.getAttribute("data-id");
            addToCart(productId);
        });
    });

    function addToCart(productId) {
        fetch("add_to_cart.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById("carrito-count").textContent = data.total_items;
        })
        .catch(error => console.error("Error:", error));
    }
});
