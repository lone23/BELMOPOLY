const celle = [
    "go-cell", "cell-1", "cell-2", "cell-3", "cell-4", "cell-5", "cell-6", "cell-7", "cell-8", "cell-9",
    "cell-10", "cell-11", "cell-12", "cell-13", "cell-14", "cell-15", "cell-16", "cell-17", "cell-18", "cell-19",
    "cell-20", "cell-21", "cell-22", "cell-23", "cell-24", "cell-25", "cell-26", "cell-27", "cell-28", "cell-29",
    "cell-30", "cell-31", "cell-32", "cell-33", "cell-34", "cell-35", "cell-36", "cell-37", "cell-38", "cell-39"
];

let passi;
let price = 0;

let Giocatori;
let posizioniGiocatori = {};
const coloriAssegnati = {};
const coloriPedine = ["blue", "green", "purple", "orange", "cyan", "yellow"];

let intervalAnimazione;
let destinazioneVeloce = null;
let mostraCartaNormaleDopoSpostamento = false;



let isMyTurn = false;
let socket = new WebSocket('ws://localhost:4000');

prendiPosizioneGiocatori();

socket.onopen = () => {
    const joinMessage = {
        joinRoom: UUID,
        playerId: id
    };
    socket.send(JSON.stringify(joinMessage));
};

socket.onmessage = (event) => {
    const data = JSON.parse(event.data);
    prendiPosizioneGiocatori();
    aggiornaSaldo();
    if (data.turn !== undefined) {
        isMyTurn = data.turn;

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
    }
}


socket.onclose = () => {
};

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



function prendiPosizioneGiocatori() {
    fetch(url + 'Board/prendiPosizionePedina')
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            const posizioni = data;

            for (const [username, posizione] of Object.entries(posizioni)) {
                posizioniGiocatori[username] = posizione;
            }


            posizioniGiocatori[usernameAttuale] = posizioni[usernameAttuale];

            disegnaTutteLePedine();
        })
        .catch(function(error) {
            console.log('Error:', error);
        });
}


function disegnaTutteLePedine() {
    const pedinaSingola = document.getElementById("pedina");
    if (pedinaSingola) pedinaSingola.remove();

    celle.forEach(id => {
        const cella = document.getElementById(id);
        if (cella) cella.querySelectorAll(".pedina").forEach(p => p.remove());
    });

    const cellaMia = document.getElementById(celle[posizioniGiocatori[usernameAttuale]]);
    if (cellaMia) {
        const miaDiv = document.createElement("div");
        miaDiv.className = "pedina";
        miaDiv.style.backgroundColor = "red";
        cellaMia.appendChild(miaDiv);
    }

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
        return;
    }
    if (muove) {
        return;
    }

    document.getElementById("rettangoloDado1").disabled = true;
    document.getElementById("rettangoloDado2").disabled = true;
    muove = true;

    let count = 0;
    const maxCount = 20;

    const rettangoloDado1 = document.getElementById('rettangoloDado1');
    const rettangoloDado2 = document.getElementById('rettangoloDado2');

    const interval = setInterval(() => {
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
                    aggiornaDado(rettangoloDado1, dado1);
                    aggiornaDado(rettangoloDado2, dado2);

                    intervalAnimazione = setInterval(muoviPedina, 1);
                })
                .catch(error => {
                    console.error('Errore nel recupero dei dati:', error);
                    document.getElementById("evento").innerHTML = "Errore nel recupero dei dadi.";
                    muove = false;
                });
        }
    }, 70);
}

function aggiornaSaldo(){
    fetch(url + 'Board/aggiornaSaldo')
        .then(response => response.json())
        .then(data => {
            for (let i = 0; i < 4; i++) {
                const playerId = `p${i + 1}`;
                const playerDiv = document.getElementById(playerId);

                if (playerDiv) {
                    const infoDiv = playerDiv.querySelector('.info');
                    const nomeP = infoDiv.querySelectorAll('p')[0];
                    const moneyP = infoDiv.querySelectorAll('p')[1];

                    if (data[i]) {
                        nomeP.textContent = data[i].username;
                        moneyP.textContent = data[i].saldo + '$';
                    } else {
                        nomeP.textContent = '[EMPTY]';
                        moneyP.textContent = '-';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Errore:', error);
        });
}


window.addEventListener("DOMContentLoaded", () => {
    const rettangoloDado1 = document.getElementById("rettangoloDado1");
    const rettangoloDado2 = document.getElementById("rettangoloDado2");

    aggiornaDado(rettangoloDado1, 5);
    aggiornaDado(rettangoloDado2, 5);
});


function muoviPedina() {

    celle.forEach(id => {
        const cella = document.getElementById(id);
        if (cella) {
            cella.querySelectorAll(".pedina").forEach(p => p.remove());
            const pedinaRossa = cella.querySelector("#pedina");
            if (pedinaRossa) pedinaRossa.remove();
        }
    });

    if (destinazioneVeloce !== null) {
        if (posizioniGiocatori[usernameAttuale] !== destinazioneVeloce) {
            posizioniGiocatori[usernameAttuale] = (posizioniGiocatori[usernameAttuale] + 1) % celle.length;
        } else {
            clearInterval(intervalAnimazione);
            muove = false;
            destinazioneVeloce = null;

            document.getElementById("rettangoloDado1").disabled = false;
            document.getElementById("rettangoloDado2").disabled = false;

            if (mostraCartaNormaleDopoSpostamento) {
                mostraCartaNormaleDopoSpostamento = false;
                pescaCartaNormale(29);
            }

            disegnaTutteLePedine();
            return;
        }
    } else if (passi > 0) {
        posizioniGiocatori[usernameAttuale] = (posizioniGiocatori[usernameAttuale] + 1) % celle.length;
        passi--;
    } else {
        const cellaId = celle[posizioniGiocatori[usernameAttuale]];

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
        }else if (cellaId === "cell-10" || cellaId === "cell-20") {
            salvaSaldo();
        }

        clearInterval(intervalAnimazione);
        document.getElementById("rettangoloDado1").disabled = false;
        document.getElementById("rettangoloDado2").disabled = false;
        muove = false;
    }


    fetch(url + 'Board/salvaPosizionePedina', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ posizione: posizioniGiocatori[usernameAttuale] })
    })
        .then(response => response.json())
        .then(data => {

        })
        .catch(error => {
            console.error('Errore:', error);
        });


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
            price = data['prezzo'];

            let descrizione = `<h1 class="name">${tipo}</h1><div class="information"><p>Prezzo: ${data.descrizione}</p><br>`;

            document.getElementById('descrizioneCarta').innerHTML = descrizione;
            document.getElementById('messaggioCarta').style.display = 'flex';

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
            price = data['prezzo'];
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
    const data = { idNumerico: idNumerico};
    fetch(url + 'Board/controlloProprieta', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            const occupata = !!data['possessore'];
            const idPossessore = data["id_utente"];

            fetch(url + 'Board/pescaCartaNormale?id=' + idNumerico, {
                method: 'GET'
            })
                .then(response => response.json())
                .then(dataCarta => {

                    if (!occupata) {
                        price = dataCarta['prezzo'];
                        const descrizione = `
                    <p class="name">${dataCarta.nome}</p>
                    <div class="information">
                        <p>Prezzo: ${dataCarta.prezzo}€</p>
                        <br>
                        <p>Affitto: ${dataCarta.affitto}€</p>
                        <p>Affitto Completo: ${dataCarta.affittoCompleto}€</p>
                        <p>Affitto Casa 1: ${dataCarta.affittoCasa1}€</p>
                        <p>Affitto Casa 2: ${dataCarta.affittoCasa2}€</p>
                        <p>Affitto Casa 3: ${dataCarta.affittoCasa3}€</p>
                        <p>Affitto Casa 4: ${dataCarta.affittoCasa4}€</p>
                        <p>Affitto Albergo: ${dataCarta.affittoAlbergo}€</p>
                        <p>Costo Casa: ${dataCarta.costoCasa}€</p>
                        <p>Costo Albergo: ${dataCarta.costoAlbergo}€</p>
                    </div>`;

                        document.getElementById('descrizioneCarta').innerHTML = descrizione;
                        document.getElementById('messaggioCarta').style.display = 'flex';
                        muove = true;

                    } else {
                        const descrizione = `
                    <p class="name">${dataCarta.nome}</p>
                    <div class="information">
                        <p>Proprietà di: ${data['possessore']}</p>
                        <p>Affitto da pagare: ${dataCarta.affitto}€</p>
                    </div>`;

                        document.getElementById('descrizioneCarta').innerHTML = descrizione;
                        document.getElementById('messaggioCarta').style.display = 'flex';

                        fetch(url + 'Board/pagaAffitto', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                uuid: UUID,
                                playerId: id,
                                idProprieta: idNumerico
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                controllo = data["controllo"]
                                if(controllo === "success"){
                                    aggiornaSaldo();
                                    fineTurno();
                                }else{
                                    console.log("ERRORE.");
                                }

                            })
                            .catch(error => {
                                console.error("Errore nel pagamento affitto:", error);
                            });
                    }
                })
                .catch(error => {
                    console.error('Errore nella richiesta della carta normale:', error);
                    alert('Errore nella connessione al server.');
                });
        })
        .catch(error => {
            console.error('Error:', error);
        });
}


function salvaSaldo(){
    fetch(url + '/Board/salvaSaldo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({price: price})
    })
        .then(response => {
            if (!response.ok) throw new Error("Errore HTTP: " + response.status);
            return response.json();
        }).then(data => {
        if (data.successo) {
            fineTurno();
        } else {
            console.warn("Errore lato server:", data.errore || "Aggiornamento saldo fallito");
        }
    })
        .catch(error => {
            console.error("Errore nella richiesta:", error);
        });
}

function compraProprieta() {
    fetch(url + '/board/compraProprieta', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ idProprieta: posizioniGiocatori[usernameAttuale] })
    })
        .then(response => {
            if (!response.ok) throw new Error("Errore HTTP: " + response.status);
            return response.json();
        }).then(data => {
        if(data.success) {
            alert("Proprietà acquistata con successo!");
        } else {
            alert("Errore: " + data.error);
        }
    })
        .catch(err => console.error('Errore nella richiesta', err));
}

function chiudiMessaggio(buy) {
    const messaggio = document.getElementById("messaggioCarta");
    messaggio.style.display = "none";
    
    if (destinazioneVeloce !== null) {
        muove = true;
        intervalAnimazione = setInterval(muoviPedina, 1);
    } else {
        muove = false;
        document.getElementById("rettangoloDado1").disabled = false;
        document.getElementById("rettangoloDado2").disabled = false;
    }
    if (buy == false){
        price = 0;
    }
    salvaSaldo();
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