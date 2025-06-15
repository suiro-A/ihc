console.log("si carga todo ");
const buscar = document.getElementById('buscar');

async function search (id)
{
        const tbody = document.getElementById('pacientes-body');
    let errorRecibido;
    if (id !==""){

        try {
            const res = await fetch(`/paciente/${id}`);
            // console.log(res);
            if (!res.ok) {  
                errorRecibido = await res.json();                          
                tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">${errorRecibido.er}</td></tr>`;                           
                throw new Error(`HTTP error: ${res.status}`);
                // throw new Error(errorRecibido);
            }
            let data = await res.json();

            tbody.innerHTML="";



            // Suponiendo que `data` es un array de pacientes:
            const pacientes = Array.isArray(data) ? data : [data];
            pacientes.forEach(paciente => {
                const row = document.createElement('tr');


                

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">${paciente.nombres}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${paciente.apellidos}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${paciente.dni}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${paciente.fecha_nac}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${paciente.sexo}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${paciente.telefono}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${paciente.correo}</td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        <!-- Aquí tus botones o links de acciones -->
                        <a href="/paciente/${paciente.id_paciente}/edit"><button class="text-blue-600 hover:text-blue-900">Editar</button></a>
                        <button class="text-red-600 hover:text-red-900" onclick="eliminarPaciente(${paciente.id_paciente})">Eliminar</button>
                        
                        
                    </td>
                `;
                                    // <td class="px-6 py-4 whitespace-nowrap">${paciente.ultima_cita || 'N/A'}</td>

                tbody.appendChild(row);
            });        

        } catch (error) {
        console.error('Error al obtener los datos:', error.message);

        }

        
    }
    else{
        
        tbody.innerHTML="";

    }
}


window.eliminarPaciente = async function eliminarPaciente(id){

    if (!confirm('¿Estás seguro de que deseas eliminar este paciente?')) return;

    try {
        const res = await fetch(`/paciente/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            const error = await res.json();
            throw new Error(error.message || `Error ${res.status}`);
        }

        // Eliminar la fila (padre del botón)
        // const row = btn.closest('tr');
        // row.remove();

        // alert('Paciente eliminado correctamente.');
        Swal.fire({

            


        title:  "¡Bien hecho!",
        text: "Paciente eliminado correctamente",
        icon:  "success"
        });
        
        search(buscar.value);

    } catch (err) {
        console.error('Error al eliminar paciente:', err);
        alert('Ocurrió un error al eliminar el paciente.');
    }
}



buscar.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault(); // Evita que se envíe el formulario o se haga submit
        console.log('Enter desactivado');
    }
});




buscar.addEventListener('keyup', (e)=> {


    // console.log(e.target.value);

    // let id = e.target.value;

    let id = e.target.value.trim();
    search(id);



    


});