const celle = [
    "go-cell", "cell-1", "cell-2", "cell-3", "cell-4", "cell-5", "cell-6", "cell-7", "cell-8", "cell-9",
    "cell-10", "cell-11", "cell-12", "cell-13", "cell-14", "cell-15", "cell-16", "cell-17", "cell-18", "cell-19",
    "cell-20", "cell-21", "cell-22", "cell-23", "cell-24", "cell-25", "cell-26", "cell-27", "cell-28", "cell-29",
    "cell-30", "cell-31", "cell-32", "cell-33", "cell-34", "cell-35", "cell-36", "cell-37", "cell-38", "cell-39"
];

let passi;

let Giocatori;
let posizioniGiocatori = {};
const coloriAssegnati = {};
const coloriPedine = ["blue", "green", "purple", "orange", "cyan", "yellow"];

let intervalAnimazione;
let destinazioneVeloce = null;
let mostraCartaNormaleDopoSpostamento = false;

let posizionePedina = 0;

fetch(url + 'Board/prendiPosizionePedina')
    .then(function(response) {
        return response.json();
    }).then(function(data) {
    // Estrai la posizione corretta dall'oggetto ricevuto
    const posizione = data.$Posizione;
    posizionePedina = posizione; // se vuoi aggiornare anche la tua variabile globale
    posizioniGiocatori[usernameAttuale] = posizione;
    disegnaTutteLePedine();
}).catch(function(error) {
    console.error('Error:', error);
});


let isMyTurn = false;  // Variabile che tiene traccia se è il turno del client
let socket = new WebSocket('ws://localhost:4000');

socket.onopen = () => {
    console.log("Connesso al server WebSocket");
    const joinMessage = {
        joinRoom: UUID,       // L'UUID della stanza
        playerId: id  // L'ID unico del giocatore
    };
    socket.send(JSON.stringify(joinMessage));
};

socket.onmessage = (event) => {
    const data = JSON.parse(event.data);
    console.log("Messaggio ricevuto:", data);

    if (data.turn !== undefined) {
        isMyTurn = data.turn;
        console.log(isMyTurn);

    }
};

function fineTurno() {
    if (isMyTurn) {

        isMyTurn = false;
        socket.send(JSON.stringify({
            turnEnd: true,
            room: UUID,
            playerId: id
        }));
        console.log("Turno passato");
    }
}


// Quando la connessione WebSocket si chiude
socket.onclose = () => {
    console.log("Connessione WebSocket chiusa");
};

// Quando si verifica un errore nel WebSocket
socket.onerror = (error) => {
    console.error("Errore WebSocket:", error);
};


const data = { uuid: UUID };
fetch(url + 'Board/numeroGiocatori', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
}).then(function(response) {
    return response.json();
}).then(function(data) {
    console.log(data);
    Giocatori = data;

    Giocatori.forEach((username, index) => {
        if (username !== usernameAttuale) {
            posizioniGiocatori[username] = 0;
            coloriAssegnati[username] = coloriPedine[index % coloriPedine.length];
        }
    });

}).catch(function(error) {
    console.error('Error:', error);
});

function disegnaTutteLePedine() {
    // Rimuove tutte le pedine attuali
    celle.forEach(id => {
        const cella = document.getElementById(id);
        if (cella) cella.querySelectorAll(".pedina").forEach(p => p.remove());
    });

    // Mostra la tua pedina (in rosso)
    const cellaMia = document.getElementById(celle[posizionePedina]);
    if (cellaMia) {
        const miaDiv = document.createElement("div");
        miaDiv.className = "pedina";
        miaDiv.style.backgroundColor = "red";
        cellaMia.appendChild(miaDiv);
    }

    // Mostra le pedine degli altri giocatori (solo cerchi colorati)
    for (const [username, posizione] of Object.entries(posizioniGiocatori)) {
        const cella = document.getElementById(celle[posizione]);
        if (cella) {
            const pedina = document.createElement("div");
            pedina.className = "pedina";
            pedina.style.backgroundColor = coloriAssegnati[username];
            cella.appendChild(pedina);
        }
    }
}




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
    if (!isMyTurn) {
        alert("Non è il tuo turno!");
        return;  // Blocca il tiro se non è il turno del giocatore
    }
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
    fineTurno();
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



    console.log(posizionePedina)
    const data = { posizione: posizionePedina };
    fetch(url + 'Board/salvaPosizionePedina', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(function(response) {
        return response.json();
    }).then(function(data) {
        console.log(data);
    }).catch(function(error) {
        console.error('Error:', error);
    });

    // Mostra la pedina nella nuova posizione
    disegnaTutteLePedine();

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

            descrizioneElemento.innerHTML = '<h1 class="name">' + tipo + '</h1><div class="information"><p>' + data.descrizione + '</p></div>';
            messaggioElemento.style.display = 'flex';

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
            let descrizione = `<h1 class="name">${data.nome}</h1><div class="information"><p>Prezzo: ${data.prezzo}€</p><br>`;

            if (stazioni.includes(id)) {
                descrizione += `
                        <p>Affitto 1 stazione: ${data.affitto1}€</p>
                        <p>Affitto 2 stazioni: ${data.affitto2}€</p>
                        <p>Affitto 3 stazioni: ${data.affitto3}€</p>
                        <p>Affitto 4 stazioni: ${data.affitto4}€</p>
                        </div>`;
            } else {
                descrizione += `
                        <p>Affitto 1 compagnia: ${data.affitto1}€</p>
                        <p>Affitto 2 compagnie: ${data.affitto2}€</p>
                        </div>`;
            }

            document.getElementById('descrizioneCarta').innerHTML = descrizione;
            document.getElementById('messaggioCarta').style.display = 'flex';

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
            <p class="name">${data.nome}</p>
            <div class="information">
            <p>Prezzo: ${data.prezzo}€</p>
            <br>
            <p>Affitto: ${data.affitto}€</p>
            <p>Affitto Completo: ${data.affittoCompleto}€</p>
            <p>Affitto Casa 1: ${data.affittoCasa1}€</p>
            <p>Affitto Casa 2: ${data.affittoCasa2}€</p>
            <p>Affitto Casa 3: ${data.affittoCasa3}€</p>
            <p>Affitto Casa 4: ${data.affittoCasa4}€</p>
            <p>Affitto Albergo: ${data.affittoAlbergo}€</p>
            <p>Costo Casa: ${data.costoCasa}€</p>
            <p>Costo Albergo: ${data.costoAlbergo}€</p>
            </div>`;

            document.getElementById('descrizioneCarta').innerHTML = descrizione;
            document.getElementById('messaggioCarta').style.display = 'flex';

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
        document.getElementById(selectedPlayer).className = "player clickable";
    } else {
        document.getElementById("p1").className = "player clickable";
    }
    document.getElementById(player).className = "player selected";
    selectedPlayer = player;
}