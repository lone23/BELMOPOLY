const celle = [
    "go-cell", "cell-1", "cell-2", "cell-3", "cell-4", "cell-5", "cell-6", "cell-7", "cell-8", "cell-9",
    "cell-10", "cell-11", "cell-12", "cell-13", "cell-14", "cell-15", "cell-16", "cell-17", "cell-18", "cell-19",
    "cell-20", "cell-21", "cell-22", "cell-23", "cell-24", "cell-25", "cell-26", "cell-27", "cell-28", "cell-29",
    "cell-30", "cell-31", "cell-32", "cell-33", "cell-34", "cell-35", "cell-36", "cell-37", "cell-38", "cell-39"
];

let posizionePedina = 0;
let passi;
let intervalAnimazione;
let destinazioneVeloce = null;
let mostraCartaNormaleDopoSpostamento = false;

function aggiornaDado(dieElement, value) {
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
        const puntino = document.createElement("div");
        puntino.classList.add("puntino");
        if (!dotPositions[value].includes(i)) {
            puntino.classList.add("hidden");
        }
        dieElement.appendChild(puntino);
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
        aggiornaDado(rettangoloDado1, Math.floor(Math.random() * 6) + 1);
        aggiornaDado(rettangoloDado2, Math.floor(Math.random() * 6) + 1);
        count++;

        if (count >= maxCount) {
            clearInterval(interval);
            fetch(url + 'Board/generaNumeroDati', {
                method: 'GET'
            })
                .then(response => response.json())
                .then(data => {
                    dado1 = data.dado1;
                    dado2 = data.dado2;

                    passi = dado1 + dado2;
                    aggiornaDado(rettangoloDado1, dado1);  // Mostra il dado 1
                    aggiornaDado(rettangoloDado2, dado2);  // Mostra il dado 2

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

// Per mostrare il numero 5 sui dadi a inizio game
window.addEventListener("DOMContentLoaded", () => {
    const rettangoloDado1 = document.getElementById("rettangoloDado1");
    const rettangoloDado2 = document.getElementById("rettangoloDado2");

    aggiornaDado(rettangoloDado1, 5);
    aggiornaDado(rettangoloDado2, 5);
});


function muoviPedina() {
    const cellaCorrente = document.getElementById(celle[posizionePedina]);
    if (cellaCorrente.querySelector("#pedina")) {
        cellaCorrente.querySelector("#pedina").remove();
    }

    // Muovimento verso Data Cube Matrix
    if (destinazioneVeloce !== null) {
        if (posizionePedina !== destinazioneVeloce) {
            posizionePedina = (posizionePedina + 1) % celle.length;
        } else {
            const cella = document.getElementById(celle[posizionePedina]);

            cella.innerHTML += '<div id="pedina"></div>';

            clearInterval(intervalAnimazione);
            muove = false;
            destinazioneVeloce = null;

            document.getElementById("rettangoloDado1").disabled = false;
            document.getElementById("rettangoloDado2").disabled = false;

            if (mostraCartaNormaleDopoSpostamento) {
                mostraCartaNormaleDopoSpostamento = false;
                pescaCartaNormale(29);
            }
            return;
        }

    } else if (passi > 0) {
        posizionePedina = (posizionePedina + 1) % celle.length;
        passi--;
    } else {
        const cellaId = celle[posizionePedina];

        const imprevisti = ["cell-7", "cell-22", "cell-36"];
        const probabilita = ["cell-2", "cell-17", "cell-33"];
        const speciali = ["cell-5", "cell-15", "cell-25", "cell-35", "cell-12", "cell-28"];
        const normali = ["cell-1", "cell-3", "cell-6", "cell-8", "cell-9", "cell-11", "cell-13", "cell-14", "cell-16", "cell-18", "cell-19", "cell-21", "cell-23", "cell-24", "cell-26", "cell-27", "cell-29", "cell-31", "cell-32", "cell-34", "cell-36", "cell-38", "cell-39"];

        if (probabilita.includes(cellaId)) {
            pescaCarta("probabilita");
        } else if (imprevisti.includes(cellaId)) {
            pescaCarta("imprevisti");
        } else if (speciali.includes(cellaId)) {
            const id = parseInt(cellaId.split("-")[1]);
            pescaCartaSpeciale(id);
        } else if (normali.includes(cellaId)) {
            const idNumerico = parseInt(cellaId.split("-")[1]);
            pescaCartaNormale(idNumerico);
        } else {
            const cella = document.getElementById(cellaId);
        }

        clearInterval(intervalAnimazione);
        document.getElementById("rettangoloDado1").disabled = false;
        document.getElementById("rettangoloDado2").disabled = false;
        muove = false;
    }

    // Mostra la pedina nella nuova posizione
    const nuovaCella = document.getElementById(celle[posizionePedina]);
    nuovaCella.innerHTML += '<div id="pedina"></div>';
}

function pescaCarta(tipo) {
    fetch(url + 'Board/pescaCarta?tipo=' + tipo, {
        method: 'GET'
    })
        .then(response => response.json())
        .then(data => {
            if (data.errore) {
                alert(data.errore);
                return;
            }

            const messaggioElemento = document.getElementById('messaggioCarta');
            const descrizioneElemento = document.getElementById('descrizioneCarta');

            descrizioneElemento.innerHTML = '<h1>' + tipo + '</h1><p>' + data.descrizione + '</p>';
            messaggioElemento.style.display = 'block';

            if (tipo === "probabilita" && data.id === 4) {
                destinazioneVeloce = celle.indexOf("cell-29");
                passi = 1000;
                mostraCartaNormaleDopoSpostamento = true;
            } else {
                muove = true;
                // Riabilita i dadi solo se NON c'è un movimento automatico in sospeso
                document.getElementById("rettangoloDado1").disabled = false;
                document.getElementById("rettangoloDado2").disabled = false;
            }

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

function pescaCartaNormale(idNumerico) {
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
            </ul>`;

            document.getElementById('descrizioneCarta').innerHTML = descrizione;
            document.getElementById('messaggioCarta').style.display = 'block';

            muove = true;
        })
        .catch(error => {
            console.error('Errore nella richiesta della carta normale:', error);
            alert('Errore nella connessione al server.');
        });
}

function chiudiMessaggio() {
    const messaggio = document.getElementById("messaggioCarta");
    messaggio.style.display = "none";

    // Se c'è un movimento automatico verso Data Cube Matrix
    if (destinazioneVeloce !== null) {
        muove = true;
        intervalAnimazione = setInterval(muoviPedina, 150);
    } else {
        muove = false;
        document.getElementById("rettangoloDado1").disabled = false;
        document.getElementById("rettangoloDado2").disabled = false;
    }
}

let selectedPlayer;
function showPossession(player){
    if (selectedPlayer){
        document.getElementById(selectedPlayer).className = "player";
    } else {
        document.getElementById("p1").className = "player";
    }
    document.getElementById(player).className = "player selected";
    selectedPlayer = player;
}