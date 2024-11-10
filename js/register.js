// console.log("Script de registro cargado"); // Confirmación de carga del script

// document.addEventListener('DOMContentLoaded', () => {
//     const formulario = document.getElementById('registro');
//     const mensajeDiv = document.getElementById("mensaje");

//     if (formulario) {
//         formulario.addEventListener('submit', (event) => {
//             event.preventDefault(); // Evita el envío tradicional del formulario

//             // Crear el objeto FormData para enviar los datos del formulario
//             const formData = new FormData(formulario);

//             // Realizar la petición AJAX con fetch
//             fetch('php/register.php', {
//                 method: 'POST',
//                 body: formData
//             })
//             .then(response => {
//                 // Verificar si la respuesta es exitosa y el contenido es JSON
//                 if (!response.ok) {
//                     throw new Error(`Error HTTP: ${response.status}`);
//                 }
//                 return response.json(); // Convertir la respuesta a JSON
//             })
//             .then(data => {
//                 // Verificar si el JSON tiene el formato esperado
//                 if (data.success) {
//                     mensajeDiv.style.color = "green";
//                     mensajeDiv.textContent = data.message;
//                     formulario.reset(); // Limpiar el formulario después del éxito
//                 } else {
//                     mensajeDiv.style.color = "red";
//                     mensajeDiv.textContent = data.message;
//                 }
//             })
//             .catch(error => {
//                 console.error("Error en la petición:", error);
//                 mensajeDiv.style.color = "red";
//                 mensajeDiv.textContent = "Hubo un problema con el registro. Por favor, inténtelo de nuevo.";
//             });
//         });
//     }
// });
