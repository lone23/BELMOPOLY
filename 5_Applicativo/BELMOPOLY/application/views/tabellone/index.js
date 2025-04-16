const celle = [
    "go-cell", "cell-1", "cell-2", "cell-3", "cell-4", "cell-5", "cell-6", "cell-7", "cell-8", "cell-9",
    "cell-10", "cell-11", "cell-12", "cell-13", "cell-14", "cell-15", "cell-16", "cell-17", "cell-18", "cell-19",
    "cell-20", "cell-21", "cell-22", "cell-23", "cell-24", "cell-25", "cell-26", "cell-27", "cell-28", "cell-29",
    "cell-30", "cell-31", "cell-32", "cell-33", "cell-34", "cell-35", "cell-36", "cell-37", "cell-38", "cell-39"
];

let posizionePedina = 1; // Posizione iniziale della pedina (GO!)
let passi;
let intervalAnimazione;

function updateDie(dieElement, value) {
    const dotPositions = {
        1: [4],
        2: [0, 8],
        3: [0, 4, 8],
        4: [0, 2, 6, 8],
        5: [0, 2, 4, 6, 8],
        6: [0, 2, 3, 5, 6, 8]
    };

    dieElement.innerHTML = "";

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
        return;  // Non può tirare i dadi finché non chiude il messaggio
    }

    document.getElementById("rettangoloDado1").disabled = true;
    document.getElementById("rettangoloDado2").disabled = true;
    muove = true;

    let count = 0;
    const maxCount = 20;

    const rettangoloDado1 = document.getElementById('rettangoloDado1');
    const rettangoloDado2 = document.getElementById('rettangoloDado2');

    const interval = setInterval(() => {
        // Animazione dei dadi (mostra valori temporanei)
        updateDie(rettangoloDado1, Math.floor(Math.random() * 6) + 1);
        updateDie(rettangoloDado2, Math.floor(Math.random() * 6) + 1);
        count++;

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
                    intervalAnimazione = setInterval(muoviPedina, 500);
                })
                .catch(error => {
                    console.error('Errore nel recupero dei dati:', error);
                    document.getElementById("evento").innerHTML = "Errore nel recupero dei dadi.";
                    muove = false;
                });
        }
    }, 70);
}


function muoviPedina() {
    const cellaCorrente = document.getElementById(celle[posizionePedina]);
    if (cellaCorrente.querySelector("#pedina")) {
        cellaCorrente.querySelector("#pedina").remove();
    }

    if (passi > 0) {
        posizionePedina = (posizionePedina + 1) % celle.length;
        passi--;
    } else {
        const cellaId = celle[posizionePedina];

        const possibilita = ["cell-7", "cell-22", "cell-36"];
        const imprevisti = ["cell-2", "cell-17", "cell-33"];
        const speciali = ["cell-5", "cell-15", "cell-25", "cell-35", "cell-12", "cell-28"];
        const normali = ["go-cell", "cell-10", "cell-20", "cell-30", "cell-7", "cell-22", "cell-36", "cell-2", "cell-17", "cell-33", "cell-5", "cell-15", "cell-25", "cell-35", "cell-12", "cell-28"];

        if (possibilita.includes(cellaId)) {
            pescaCarta("possibilita");
        } else if (imprevisti.includes(cellaId)) {
            pescaCarta("imprevisti");
        } else if (speciali.includes(cellaId)) {
            const id = parseInt(cellaId.split("-")[1]); // estrae il numero dopo "cell-"
            pescaCartaSpeciale(id);
        } else if (!normali.includes(cellaId)) {
            // Se non è una casella esclusa, pesca la carta normale
            pescaCartaNormale();
        } else {
            const cella = document.getElementById(cellaId);
            const testo = cella.querySelector("p") ? cella.querySelector("p").textContent : "nulla";
            document.getElementById("evento").innerHTML = "SEI SULLA CASELLA " + testo;
        }

        clearInterval(intervalAnimazione);
        document.getElementById("rettangoloDado1").disabled = false;
        document.getElementById("rettangoloDado2").disabled = false;
        muove = false;
    }

    const nuovaCella = document.getElementById(celle[posizionePedina]);
    nuovaCella.innerHTML += '<div id="pedina"></div>';
}

function pescaCarta(tipo) {
    fetch(url + 'Board/pescaCarta?tipo=' + tipo, {
        method: 'GET'
    })
        .then(response => response.text())
        .then(data => {
            const messaggioElemento = document.getElementById('messaggioCarta');
            const descrizioneElemento = document.getElementById('descrizioneCarta');

            descrizioneElemento.innerHTML = '<h1>'+tipo+'</h1>' + '<p>'+data+'</p>';

            messaggioElemento.style.display = 'block';
            muove = true;
        })
        .catch(error => {
            console.error('Errore nel recupero della carta:', error);
            alert('Errore nella connessione al server.');
        });
}

function pescaCartaSpeciale(id) {
    fetch(url + 'Board/pescaCartaSpeciale?id=' + id, {
        method: 'GET'
    })
        .then(response => response.json())
        .then(data => {
            const stazioni = [5, 15, 25, 35];
            let descrizione = `<h1>${data.nome}</h1><p>Prezzo: ${data.prezzo}€</p>`;

            if (stazioni.includes(id)) {
                descrizione += `
                    <ul>
                        <li>Affitto 1 stazione: ${data.affitto1}€</li>
                        <li>Affitto 2 stazioni: ${data.affitto2}€</li>
                        <li>Affitto 3 stazioni: ${data.affitto3}€</li>
                        <li>Affitto 4 stazioni: ${data.affitto4}€</li>
                    </ul>
                `;
            } else {
                descrizione += `
                    <ul>
                        <li>Affitto 1 compagnia: ${data.affitto1}€</li>
                        <li>Affitto 2 compagnie: ${data.affitto2}€</li>
                    </ul>
                `;
            }

            document.getElementById('descrizioneCarta').innerHTML = descrizione;
            document.getElementById('messaggioCarta').style.display = 'block';

            muove = true;
        })
        .catch(error => {
            console.error('Errore nella richiesta della carta speciale:', error);
        });
}

function pescaCartaNormale() {
    // Ottiene l'ID della cella corrente
    const cellaId = celle[posizionePedina]; // es. "cell-3"

    // Estrae solo la parte numerica, es. "3"
    const idNumerico = parseInt(cellaId.split("-")[1]);

    // Effettua la richiesta al backend passando l'ID numerico
    fetch(url + 'Board/pescaCartaNormale?id=' + idNumerico, {
        method: 'GET'
    })
        .then(response => response.json())
        .then(data => {
            const descrizione = `
            <h1>${data.nome}</h1>
            <p>Prezzo: ${data.prezzo}€</p>
            <ul>
                <li>Affitto: ${data.affitto}€</li>
                <li>Affitto Completo: ${data.affittoCompleto}€</li>
                <li>Affitto Casa 1: ${data.affittoCasa1}€</li>
                <li>Affitto Casa 2: ${data.affittoCasa2}€</li>
                <li>Affitto Casa 3: ${data.affittoCasa3}€</li>
                <li>Affitto Casa 4: ${data.affittoCasa4}€</li>
                <li>Affitto Albergo: ${data.affittoAlbergo}€</li>
                <li>Costo Casa: ${data.costoCasa}€</li>
                <li>Costo Albergo: ${data.costoAlbergo}€</li>
            </ul>
        `;

            // Mostra la descrizione
            document.getElementById('descrizioneCarta').innerHTML = descrizione;
            document.getElementById('messaggioCarta').style.display = 'block';

            // Abilita eventuali altre azioni
            muove = true;
        })
        .catch(error => {
            console.error('Errore nella richiesta della carta normale:', error);
            alert('Errore nella connessione al server.');
        });
}
