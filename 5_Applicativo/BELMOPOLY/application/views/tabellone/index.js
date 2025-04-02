const celle = [
    "go-cell", "cell-1", "cell-2", "cell-3", "cell-4", "cell-5", "cell-6", "cell-7", "cell-8", "cell-9",
    "cell-10", "cell-11", "cell-12", "cell-13", "cell-14", "cell-15", "cell-16", "cell-17", "cell-18", "cell-19",
    "cell-20", "cell-21", "cell-22", "cell-23", "cell-24", "cell-25", "cell-26", "cell-27", "cell-28", "cell-29",
    "cell-30", "cell-31", "cell-32", "cell-33", "cell-34", "cell-35", "cell-36", "cell-37", "cell-38", "cell-39"
];

let posizionePedina = 0; // Posizione iniziale della pedina (GO!)
let passi;
let intervalAnim;

function updateDie(dieElement, value) {
    const dotPositions = {
        1: [4],
        2: [0, 8],
        3: [0, 4, 8],
        4: [0, 2, 6, 8],
        5: [0, 2, 4, 6, 8],
        6: [0, 2, 3, 5, 6, 8]
    };

    dieElement.innerHTML = ""; // Svuota il contenitore del dado

    for (let i = 0; i < 9; i++) {
        const dot = document.createElement("div");
        dot.classList.add("puntino");
        if (!dotPositions[value].includes(i)) {
            dot.classList.add("hidden");
        }
        dieElement.appendChild(dot);
    }
}

let dado1;
let dado2;
let muove = false;

function tiraDadi() {
    if (muove) {
        return;
    }

    // Disabilita i pulsanti durante il lancio
    document.getElementById("rettangoloDado1").disabled = true;
    document.getElementById("rettangoloDado2").disabled = true;
    muove = true;

    let count = 0;
    const maxCount = 20;  // Tempo in cui l'animazione dei dadi si esegue

    const rettangoloDado1 = document.getElementById('rettangoloDado1');
    const rettangoloDado2 = document.getElementById('rettangoloDado2');

    const interval = setInterval(() => {
        // Animazione dei dadi (mostra valori temporanei)
        updateDie(rettangoloDado1, Math.floor(Math.random() * 6) + 1);  // Numeri casuali per animazione
        updateDie(rettangoloDado2, Math.floor(Math.random() * 6) + 1);  // Numeri casuali per animazione
        count++;

        // Dopo un certo tempo fermiamo l'animazione e prendiamo i dati effettivi
        if (count >= maxCount) {
            clearInterval(interval);

            // Recupera i numeri reali dal PHP
            fetch(url + 'Board/generaNumeroDati', {
                method: 'GET'
            })
                .then(response => response.json())
                .then(data => {
                    dado1 = data.dado1;
                    dado2 = data.dado2;

                    // Calcola il numero di passi e avvia il movimento
                    passi = dado1 + dado2;
                    updateDie(rettangoloDado1, dado1);  // Mostra il dado 1
                    updateDie(rettangoloDado2, dado2);  // Mostra il dado 2

                    // Avvia il movimento della pedina
                    intervalAnim = setInterval(muoviPedina, 500);
                })
                .catch(error => {
                    console.error('Errore nel recupero dei dati:', error);
                    document.getElementById("evento").innerHTML = "Errore nel recupero dei dadi.";
                    muove = false;
                });
        }
    }, 70);  // Frequenza dell'animazione dei dadi (ogni 50ms)
}


// Function to move the piece
function muoviPedina() {
    // Remove the piece from the current cell
    const cellaCorrente = document.getElementById(celle[posizionePedina]);
    if (cellaCorrente.querySelector("#pedina")) {
        cellaCorrente.querySelector("#pedina").remove();
    }

    // Move the piece based on the number of steps
    if (passi > 0) {
        posizionePedina = (posizionePedina + 1) % celle.length;
        passi--;
    } else {
        let cella = document.getElementById(celle[posizionePedina]);
        let testo = cella.querySelector("p") ? cella.querySelector("p").textContent : "nulla";
        clearInterval(intervalAnim);
        document.getElementById("evento").innerHTML = "SEI SULLA CASELLA " + testo;

        document.getElementById("rettangoloDado1").disabled = false;
        document.getElementById("rettangoloDado2").disabled = false;
        muove = false;
    }

    // Add the piece to the new cell
    const nuovaCella = document.getElementById(celle[posizionePedina]);
    nuovaCella.innerHTML += '<div id="pedina"></div>';
}