<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lingo Linty</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    <header>
        <img src="{{ asset('images/mi-logo.png') }}" alt="Logo" class="logo">
        <form id="logoutForm">
            <button id="logoutButton" type="submit">Cerrar sesión</button>
        </form>
    </header>

    

    <nav>

        <a href="#"><img src="{{ asset('Iconos/IconoCasa.png') }}"alt=""></a>
        <a href="#" id="btn-config"><img src="{{ asset('Iconos/IconoConfiguracion.png') }}" alt="Configuración"></a>
        <a href="http://localhost/ranking"><img src="{{ asset('Iconos/IconoEstadistica.png') }}" alt=""></a>

    </nav>

    <div id="contenedor"></div>

    <div id="temporizador-flotante"
        style="display: none; position: absolute; height: 20px; background-color: green; color: white; text-align: center; line-height: 20px; border-radius: 5px; font-weight: bold;">
        <div id="barra-tiempo"></div>
    </div>



    <div id="teclado"></div>

    <!-- Pop-up de reglas -->
    <div id="popup-reglas-fondo">
        <div id="popup-reglas">
            <h2>Reglas del Lingo</h2>
            <p>
                Adivina la palabra de 5 letras en un máximo de 5 intentos.<br><br>
                - 🟩 Verde: la letra está en el lugar correcto.<br>
                - 🟧 Naranja: la letra está en la palabra, pero en otra posición.<br>
                - 🟥 Rojo: la letra no está en la palabra.<br><br>
                Cada intento tiene tiempo limitado, ¡sé rápido! ⏱️
            </p>
            <button id="btn-iniciar">Iniciar partida</button>
        </div>
    </div>

    <!-- POPUP DE CONFIGURACIÓN -->
    <div id="popup-config-fondo">
        <div id="popup-config">
            <h2>Configuración ⚙️</h2>

            <div class="config-opcion">
                <label>
                    <input type="checkbox" id="sonido" checked>
                    Activar sonidos
                </label>
            </div>

            <div class="config-opcion">
                <label for="tiempo-ronda">⏱️ Tiempo por ronda (segundos):</label>
                <input type="number" id="tiempo-ronda" min="5" max="60" value="15">
            </div>

            <div class="config-opcion">
                <button id="reiniciar-progreso" class="boton-secundario">Reiniciar progreso</button>
            </div>

            <button id="cerrar-config">Guardar y cerrar</button>
        </div>
    </div>



    <div id="popup-fondo">
        <div id="popup">
            <h2>¡Has ganado!</h2>
            <p id="popup-rondas">🏆 Rondas ganadas: 0</p>
            <p id="popup-puntuacion">🏆 Puntuación total: 0</p>
            <button onclick="nuevaRondaPopup()">Jugar otra ronda</button>
        </div>
    </div>



    <div id="mensaje" style="display:none; text-align:center; margin-top:20px;">
        <p id="textoMensaje"></p>
        <button onclick="nuevaRonda()">Jugar otra ronda</button>
    </div>

<div id="logoutPopupFondo">
    <div id="logoutPopup">
        <p>Cerrando sesión...</p>
    </div>
</div>

    <footer>

        <a href=""><img src="{{ asset('Iconos/LogoInstagram.png') }}" alt=""></a>
        <a href=""><img src="{{ asset('Iconos/LogoTwitter.png') }}" alt=""></a>
        <a href=""><img src="{{ asset('Iconos/LogoYoutube.png') }}" alt=""></a>


    </footer>

    <script>
        let puntuacionTotal = 0;
        let rondasGanadas = 0;
        let juegoTerminado = false;
        const N = 5;
        let filaActual = 0;
        let columnaActual = 0;
        const contenedor = document.getElementById("contenedor");
        const teclado = document.getElementById("teclado");



        let tiempoRestante = 15;
        let temporizador;
        let tiempoPorFila = 15;


document.addEventListener('DOMContentLoaded', function() {
    const logoutForm = document.getElementById('logoutForm');
    const logoutPopupFondo = document.getElementById('logoutPopupFondo');
    const logoutButton = document.getElementById('logoutButton');

    logoutForm.addEventListener('submit', function(e) {
        e.preventDefault();
        logoutButton.disabled = true;
        logoutPopupFondo.classList.add('mostrar');


    });
});  

        document.getElementById('logoutForm').addEventListener('submit', async function (e) {
            e.preventDefault(); // evita que el navegador haga el POST tradicional

            // 🔐 Llama al endpoint de logout de Laravel
            await fetch('/logout', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCookie('XSRF-TOKEN'),
                    'Content-Type': 'application/json'
                },
                credentials: 'include'
            });

            // 🔄 Redirige al welcome después del logout
            window.location.href = '/';
        });

        // Función auxiliar para leer la cookie CSRF que Breeze/Sanctum genera
        function getCookie(name) {
            const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
            return match ? decodeURIComponent(match[2]) : null;
        }

        let palabraCorrecta = ""; // inicializamos vacía
        const ENDPOINT_URL = "http://185.60.43.155:3000/api/word/1";

        // 🔹 Al cargar la página, obtenemos la palabra antes de iniciar el juego
        window.addEventListener("DOMContentLoaded", () => {
            // Mostrar popup de reglas
            const fondo = document.getElementById("popup-reglas-fondo");
            const popup = document.getElementById("popup-reglas");
            setTimeout(() => popup.classList.add("mostrar"), 50);

            // Desactivar el resto del juego hasta presionar iniciar
            document.getElementById("btn-iniciar").addEventListener("click", async () => {
                popup.classList.remove("mostrar");
                setTimeout(() => fondo.style.display = "none", 300);

                // Ahora sí: iniciar el juego
                palabraCorrecta = await obtenerPalabraDeAPI();
                console.log("✅ Palabra cargada:", palabraCorrecta);
                iniciarTemporizador();
            });
        });




        // Función para obtener la palabra de la API
        async function obtenerPalabraDeAPI() {
            try {
                const resp = await fetch(ENDPOINT_URL);
                if (!resp.ok) throw new Error(`Error HTTP ${resp.status}`);

                const data = await resp.json();
                if (data.word) {
                    return data.word.toUpperCase();
                } else {
                    console.error("No se encontró 'word' en la respuesta:", data);
                    return "ERROR";
                }
            } catch (error) {
                console.error("Error al obtener la palabra de la API:", error);
                alert("No se pudo cargar la palabra. Usando 'LINGO' por defecto.");
                return "LINGO";
            }
        }

        // Inicialización del juego
        async function iniciarJuego() {
            palabraCorrecta = await obtenerPalabraDeAPI();
            console.log("✅ Palabra cargada:", palabraCorrecta);

            iniciarTemporizador();
        }


        function manejarClickTecla(letra) {
            if (juegoTerminado) return; // bloquear si terminó el juego
            if (columnaActual >= N) return; // no permitir más letras
            const celda = document.getElementById(`Letra${filaActual}${columnaActual}`);
            celda.textContent = letra;
            celda.classList.add("celda-activa");
            columnaActual++;

        }

        function borrarLetra() {
            if (juegoTerminado) return; // bloquear si terminó el juego
            if (columnaActual > 0) {
                columnaActual--;
                const celda = document.getElementById(`Letra${filaActual}${columnaActual}`);
                celda.textContent = "";
                celda.classList.remove("celda-activa");

            }
        }


        async function presionarEnter() {
            if (juegoTerminado) return;

            if (columnaActual !== N) {
                alert("Debes completar la fila antes de presionar Enter");
                return;
            }

            // 🔹 Construir palabra introducida
            let palabraIntento = "";
            for (let i = 0; i < N; i++) {
                const celda = document.getElementById(`Letra${filaActual}${i}`);
                palabraIntento += (celda.textContent || "").trim().toLowerCase();
            }

            // 🔹 Validar palabra con la API
            const valida = await validarPalabra(palabraIntento);
            if (!valida) {
                // ❌ Si la palabra no existe, se pierde automáticamente la fila
                for (let i = 0; i < N; i++) {
                    const celda = document.getElementById(`Letra${filaActual}${i}`);
                    celda.classList.remove("celda-verde", "celda-naranja", "celda-activa");
                    celda.classList.add("celda-rojo");
                }

                // Avanzar a la siguiente fila
                filaActual++;
                columnaActual = 0;

                // Si ya no hay más filas → perder partida
                if (filaActual >= N) {
                    detenerTemporizador();
                    mostrarPopup(`❌ Has perdido. La palabra era ${palabraCorrecta}`);
                    juegoTerminado = true;
                } else {
                    iniciarTemporizador();
                }

                return; // salir de la función
            }


            // 🔹 Comparar con la palabra correcta
            const letraCuenta = {};
            for (let letra of palabraCorrecta) {
                letraCuenta[letra] = (letraCuenta[letra] || 0) + 1;
            }

            const colores = Array(N).fill("R"); // rojo por defecto

            // 1️⃣ Verdes (correctas en posición)
            for (let i = 0; i < N; i++) {
                const celda = document.getElementById(`Letra${filaActual}${i}`);
                const letra = celda.textContent.toUpperCase();
                if (letra === palabraCorrecta[i]) {
                    colores[i] = "V";
                    letraCuenta[letra]--;
                }
            }

            // 2️⃣ Naranjas (en palabra pero posición incorrecta)
            for (let i = 0; i < N; i++) {
                if (colores[i] === "R") {
                    const celda = document.getElementById(`Letra${filaActual}${i}`);
                    const letra = celda.textContent.toUpperCase();
                    if (letraCuenta[letra] > 0) {
                        colores[i] = "N";
                        letraCuenta[letra]--;
                    }
                }
            }

            // 3️⃣ Aplicar colores visuales
            for (let i = 0; i < N; i++) {
                const celda = document.getElementById(`Letra${filaActual}${i}`);
                const letra = celda.textContent.toUpperCase();
                celda.classList.remove("celda-verde", "celda-naranja", "celda-rojo", "celda-activa");

                const claseColor = colores[i] === "V" ? "celda-verde" :
                    colores[i] === "N" ? "celda-naranja" : "celda-rojo";

                celda.classList.add(claseColor);

                // Actualizar teclado
                const tecla = document.querySelector(`#Tecla${letra}`);
                if (tecla) {
                    if (claseColor === "celda-verde") {
                        tecla.classList.remove("tecla-naranja", "tecla-rojo");
                        tecla.classList.add("tecla-verde");
                    } else if (claseColor === "celda-naranja") {
                        if (!tecla.classList.contains("tecla-verde")) {
                            tecla.classList.remove("tecla-rojo");
                            tecla.classList.add("tecla-naranja");
                        }
                    } else if (claseColor === "celda-rojo") {
                        if (!tecla.classList.contains("tecla-verde") && !tecla.classList.contains("tecla-naranja")) {
                            tecla.classList.add("tecla-rojo");
                        }
                    }
                }
            }

            // 4️⃣ Comprobar si la palabra es correcta
            const filaCorrecta = colores.every(c => c === "V");

            if (filaCorrecta) {
                rondasGanadas++;
                const puntosFila = Math.max(0, tiempoRestante) * 10 * (N - filaActual);
                puntuacionTotal += puntosFila;
                detenerTemporizador();
                mostrarPopup();
                juegoTerminado = true;
            } else if (filaActual >= N - 1) {
                detenerTemporizador();
                mostrarPopup(`¡Has perdido! La palabra era ${palabraCorrecta}`);
                juegoTerminado = true;
            }

            filaActual++;
            columnaActual = 0;

            if (!juegoTerminado && filaActual < N) {
                iniciarTemporizador();
            } else {
                detenerTemporizador();
            }
        }

        // 🔹 Nueva función para validar palabra
        async function validarPalabra(palabra) {
            try {
                const resp = await fetch(`http://185.60.43.155:3000/api/check/${palabra}`);
                if (!resp.ok) throw new Error(`Error HTTP ${resp.status}`);
                const data = await resp.json();
                return data.exists === true;
            } catch (err) {
                console.error("Error al validar palabra:", err);
                alert("⚠️ No se pudo comprobar la palabra. Intenta de nuevo.");
                return false;
            }
        }




        const anchoInicial = 200; // nuevo ancho de la barra

        function iniciarTemporizador() {
            clearInterval(temporizador);
            tiempoRestante = tiempoPorFila;

            const temporizadorDiv = document.getElementById("temporizador-flotante");
            actualizarPosicionTemporizador();
            temporizadorDiv.style.width = `${anchoInicial}px`; // ancho inicial
            temporizadorDiv.textContent = `${tiempoRestante}s`;

            temporizador = setInterval(() => {
                tiempoRestante--;
                temporizadorDiv.textContent = `${tiempoRestante}s`;

                const porcentaje = (tiempoRestante / tiempoPorFila) * anchoInicial;
                temporizadorDiv.style.width = `${porcentaje}px`; // disminuye proporcionalmente

                if (tiempoRestante <= 0) {
                    clearInterval(temporizador);
                    alert("⏰ ¡Se acabó el tiempo para esta fila!");
                    filaActual++;
                    columnaActual = 0;

                    if (filaActual >= N) {
                        mostrarPopup(`¡Has perdido! La palabra era ${palabraCorrecta}`);

                        // Reiniciar puntuación y rondas
                        rondasGanadas = 0;
                        puntuacionTotal = 0;

                        juegoTerminado = true;
                        temporizadorDiv.style.display = "none";
                    } else {
                        iniciarTemporizador();
                    }
                }
            }, 1000);
        }





        function detenerTemporizador() {
            if (temporizador) clearInterval(temporizador);
        }




        function mostrarMensaje(texto) {
            const div = document.getElementById("mensaje");
            const p = document.getElementById("textoMensaje");
            p.textContent = texto;
            div.style.display = "block";
        }

        async function nuevaRonda() {
            // Si el juego estaba perdido, reiniciar puntuación y rondas
            if (juegoTerminado && filaActual >= N) {
                rondasGanadas = 0;
                puntuacionTotal = 0;
            }

            // Ocultar mensaje y popup
            document.getElementById("mensaje").style.display = "none";
            cerrarPopup();

            juegoTerminado = false; // desbloquea el juego

            // Resetear variables
            filaActual = 0;
            columnaActual = 0;

            // Resetear celdas del tablero
            for (let i = 0; i < N; i++) {
                for (let j = 0; j < N; j++) {
                    const celda = document.getElementById(`Letra${i}${j}`);
                    celda.textContent = "";
                    celda.classList.remove("celda-verde", "celda-naranja", "celda-rojo", "celda-activa");
                }
            }

            // Resetear colores del teclado
            const teclas = document.querySelectorAll(".tecla");
            teclas.forEach(tecla => {
                tecla.classList.remove("tecla-verde", "tecla-naranja", "tecla-rojo");
            });

            // 🟩 NUEVO: Obtener una nueva palabra antes de iniciar
            palabraCorrecta = await obtenerPalabraDeAPI();
            console.log("🔄 Nueva palabra cargada:", palabraCorrecta);

            // Iniciar temporizador
            iniciarTemporizador();
        }


        let sHTMLContenedor = '<table class="tablero">';
        for (let i = 0; i < N; i++) {
            sHTMLContenedor += "<tr>";
            for (let j = 0; j < N; j++) {
                sHTMLContenedor += `<td id="Letra${i}${j}" class="celda"></td>`;
            }
            sHTMLContenedor += "</tr>";
        }
        sHTMLContenedor += "</table>";
        contenedor.innerHTML = sHTMLContenedor;

        const filasTeclado = [
            ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'],
            ['J', 'K', 'L', 'M', 'N', 'Ñ', 'O', 'P', 'Q'],
            ['R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'],
            ['Borrar', 'Enter'] // fila especial
        ];

        let sHTMLTeclado = '';

        filasTeclado.forEach(fila => {
            sHTMLTeclado += `<div class="teclado-fila">`;
            fila.forEach(letra => {
                if (letra === 'Borrar') {
                    sHTMLTeclado +=
                        `<button id="TeclaBorrar" class="tecla tecla-especial" onclick="borrarLetra()">⌫</button>`;
                } else if (letra === 'Enter') {
                    sHTMLTeclado +=
                        `<button id="TeclaEnter" class="tecla tecla-especial tecla-enter" onclick="presionarEnter()">ENTER</button>`;
                } else {
                    sHTMLTeclado +=
                        `<button id="Tecla${letra}" class="tecla" onclick="manejarClickTecla('${letra}')">${letra}</button>`;
                }
            });
            sHTMLTeclado += `</div>`;
        });

        document.getElementById("teclado").innerHTML = sHTMLTeclado;

        function actualizarPosicionTemporizador() {
            const fila = document.querySelector(`#Letra${filaActual}0`).parentElement; // <tr>
            const temporizadorDiv = document.getElementById("temporizador-flotante");

            fila.appendChild(temporizadorDiv); // ahora la barra es hija de la fila
            temporizadorDiv.style.display = "block";
        }


        function mostrarPopup(mensaje = "¡Has ganado!") {
            document.querySelector("#popup h2").textContent = mensaje;
            document.getElementById("popup-rondas").textContent = `🏆 Rondas ganadas: ${rondasGanadas}`;
            document.getElementById("popup-puntuacion").textContent = `🏆 Puntuación total: ${puntuacionTotal}`;

            const fondo = document.getElementById("popup-fondo");
            fondo.style.display = "flex";

            setTimeout(() => {
                fondo.classList.add("mostrar");
            }, 10);
        }


        function cerrarPopup() {
            const fondo = document.getElementById("popup-fondo");
            fondo.classList.remove("mostrar");
            setTimeout(() => {
                fondo.style.display = "none";
            }, 300);
        }

        function nuevaRondaPopup() {
            cerrarPopup();
            nuevaRonda();
        }


        function ocultarPopup() {
            document.getElementById("popup").style.display = "none";
        }

        function actualizarBarra() {
            const barra = document.getElementById("barra-tiempo");
            const porcentaje = tiempoRestante / tiempoPorFila;

            // Ajustar ancho
            barra.style.width = `${porcentaje * 100}%`;

            // Cambiar color según porcentaje
            if (porcentaje > 0.5) {
                barra.style.backgroundColor = "#4CAF50"; // verde
            } else if (porcentaje > 0.2) {
                barra.style.backgroundColor = "#FF9800"; // naranja
            } else {
                barra.style.backgroundColor = "#F44336"; // rojo
            }
        }

        // --- POPUP DE CONFIGURACIÓN ---
        const btnConfig = document.getElementById("btn-config");
        const popupConfigFondo = document.getElementById("popup-config-fondo");
        const cerrarConfig = document.getElementById("cerrar-config");
        const reiniciarProgreso = document.getElementById("reiniciar-progreso");

        btnConfig.addEventListener("click", (e) => {
            e.preventDefault();
            popupConfigFondo.style.display = "flex";
            setTimeout(() => {
                popupConfigFondo.classList.add("mostrar");
            }, 10);
        });

        cerrarConfig.addEventListener("click", () => {
            // Guardar el nuevo tiempo de ronda si se cambió
            const nuevoTiempo = parseInt(document.getElementById("tiempo-ronda").value, 10);
            if (!isNaN(nuevoTiempo) && nuevoTiempo > 0) {
                tiempoPorFila = nuevoTiempo;
            }

            // Cerrar el popup
            popupConfigFondo.classList.remove("mostrar");
            setTimeout(() => {
                popupConfigFondo.style.display = "none";
            }, 300);
        });

        reiniciarProgreso.addEventListener("click", () => {
            puntuacionTotal = 0;
            rondasGanadas = 0;

            // Si quieres, también puedes reiniciar la ronda actual
            nuevaRonda(); // <-- llama a tu función de reiniciar partida

            // Cerrar el popup de configuración
            popupConfigFondo.classList.remove("mostrar");
            setTimeout(() => {
                popupConfigFondo.style.display = "none";
            }, 300);
        });

    </script>

</body>

</html>