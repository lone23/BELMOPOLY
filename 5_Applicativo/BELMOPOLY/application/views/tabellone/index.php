<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monopoly Game</title>
    <link rel="stylesheet" href="<?php echo URL?>application/views/styles/style.css">
    <link rel="stylesheet" href="<?php echo URL?>application/views/styles/board.css">
    <script>
        const url = "<?php echo URL; ?>";
        const UUID = "<?php echo $_COOKIE['uuid']; ?>";

    </script>
    <script src="<?php echo URL ?>application/views/tabellone/index.js"></script>
</head>
<body>
<div class="container">
    <div class="content">
        <div id="messaggioCarta" class="messaggio-carta">
            <div id="descrizioneCarta" class="description"></div>
            <div class="buttons">
                <div id="okButton" class="button" onclick="chiudiMessaggio()">BUY</div>
                <div id="okButton" class="button" onclick="chiudiMessaggio()">LEAVE</div>
            </div>
        </div>



            <table class="board"">

                    <tr class="schema">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td id="cell-20" colspan="3" rowspan="3" class="tl-corner"><p style="transform: rotate(135deg); font-size: 1vw;">Subway</p></td>
                        <td id="cell-21" colspan="2" rowspan="2"><p class="top">Neural Sector</p></td>
                        <td id="cell-22" colspan="2" rowspan="3"><p class="top">?</p></td>
                        <td id="cell-23" colspan="2" rowspan="2"><p class="top">Neural Nexus</p></td>
                        <td id="cell-24" colspan="2" rowspan="2"><p class="top">Neural Horizon</p></td>
                        <td id="cell-25" colspan="2" rowspan="2"><p class="top">Cyber Station North</p></td>
                        <td id="cell-26" colspan="2" rowspan="2"><p class="top">Datacube Network</p></td>
                        <td id="cell-27" colspan="2" rowspan="2"><p class="top">Datacube Lab</p></td>
                        <td id="cell-28" colspan="2" rowspan="2"><p class="top">Nano Company</p></td>
                        <td id="cell-29" colspan="2" rowspan="2"><p class="top">Datacube Matrix</p></td>
                        <td id="cell-30" colspan="3" rowspan="3" class="tr-corner"><p style="transform: rotate(-135deg); font-size: 1vw;">Malware</p></td>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="red"><p class="top">220$</p></td>
                        <td colspan="2" class="red"><p class="top">220$</p></td>
                        <td colspan="2" class="red"><p class="top">240$</p></td>
                        <td colspan="2" class="gray"><p class="top">200$</p></td>
                        <td colspan="2" class="yellow"><p class="top">260$</p></td>
                        <td colspan="2" class="yellow"><p class="top">260$</p></td>
                        <td colspan="2" ><p class="top">150$</p></td>
                        <td colspan="2" class="yellow"><p class="top">280$</p></td>
                        <td class="schema"></td>
                    </tr>

                    <tr>
                        <td id="cell-19" rowspan="2" colspan="2"><p class="left">Techno Factory</p></td>
                        <td rowspan="2" class="orange"><p class="left">200$</p></td>
                        <td rowspan="18" colspan="18" class="center"><p style="transform: rotate(-45deg); font-size: 5vw;">BELMOPOLY</p></td>
                        <td rowspan="2" class="green"><p class="right">300$</p></td>
                        <td id="cell-31" rowspan="2" colspan="2"><p class="right">Neural Plaza</p></td>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td id="cell-18" rowspan="2" colspan="2"><p class="left">Techno Street</p></td>
                        <td rowspan="2" class="orange"><p class="left">180$</p></td>
                        <td rowspan="2" class="green"><p class="right">300$</p></td>
                        <td id="cell-32" rowspan="2" colspan="2"><p class="right">Fusion Plaza</p></td>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td id="cell-17" rowspan="2" colspan="3"><p class="left"><img src="<?php echo URL?>application/views/images/chest.png" alt="probabilita" class="casella-img"></p></td>
                        <td id="cell-33" rowspan="2" colspan="3"><p class="right"><img src="<?php echo URL?>application/views/images/chest.png" alt="probabilita" class="casella-img"></p></td>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td id="cell-16" rowspan="2" colspan="2"><p class="left">Techno Bridge</p></td>
                        <td rowspan="2" class="orange"><p class="left">180$</p></td>
                        <td rowspan="2" class="green"><p class="right">320$</p></td>
                        <td id="cell-34" rowspan="2" colspan="2"><p class="right">Zero Gravity Plaza</p></td>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td id="cell-15" rowspan="2" colspan="2"><p class="left">Cyber Station West</p></td>
                        <td rowspan="2" class="gray"><p class="left">200$</p></td>
                        <td rowspan="2" class="gray"><p class="right">200$</p></td>
                        <td id="cell-35" rowspan="2" colspan="2"><p class="right">Cyber Station Est</p></td>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td id="cell-14" rowspan="2" colspan="2"><p class="left">Plasma Reactor</p></td>
                        <td rowspan="2" class="magenta"><p class="left">160$</p></td>
                        <td id="cell-36" rowspan="2" colspan="3"><p class="right">?</p></td>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td id="cell-13" rowspan="2" colspan="2"><p class="left">Plasma Circuit</p></td>
                        <td rowspan="2" class="magenta"><p class="left">140$</p></td>
                        <td rowspan="2" class="blue"><p class="right">350$</p></td>
                        <td id="cell-37" rowspan="2" colspan="2"><p class="right">Cybercore Plaza</p></td>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td id="cell-12" rowspan="2" colspan="2"><p class="left">Holo Company</p></td>
                        <td rowspan="2"><p class="left">150$</p></td>
                        <td id="cell-38" rowspan="2" colspan="3"><p class="right">-100$</p></td>
                        <td class="schema"></td>
                    </tr>
                    <tr>

                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td id="cell-11" rowspan="2" colspan="2"><p class="left">Plasma Avenue</p></td>
                        <td rowspan="2" class="magenta"><p class="left">140$</p></td>
                        <td rowspan="2" class="blue"><p class="right">400$</p></td>
                        <td id="cell-39" rowspan="2" colspan="2"><p class="right">Megacity Plaza</p></td>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td class="schema"></td>
                    </tr>

                    <tr>
                        <td id="cell-10" class="bl-corner" colspan="3" rowspan="3"><p style="transform: rotate(45deg); font-size: 1vw;">Virtual Lock</p></td>
                        <td colspan="2" class="cyan"><p>120$</p></td>
                        <td colspan="2" class="cyan"><p>100$</p></td>
                        <td id="cell-7" colspan="2" rowspan="3"><p>?</p></td>
                        <td colspan="2" class="cyan"><p>100$</p></td>
                        <td colspan="2" class="gray"><p>200$</p></td>
                        <td id="cell-4" colspan="2" rowspan="3"><p>-200$</p></td>
                        <td colspan="2" class="brown"><p>60$</p></td>
                        <td id="cell-2" colspan="2" rowspan="3"><img src="<?php echo URL?>application/views/images/chest.png" alt="probabilita" class="casella-img"></td>
                        <td colspan="2" class="brown"><p>60$</p></td>
                        <td colspan="3" rowspan="3" id="go-cell" class="br-corner" style="position: relative;">
                            <p style="transform: rotate(-45deg); font-size: 2vw;">GO!</p>
                            <div id="pedina"></div>
                        </td>

                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td id="cell-9" colspan="2" rowspan="2"><p>Neon Core</p></td>
                        <td id="cell-8" colspan="2" rowspan="2"><p>Neon Tower</p></td>
                        <td id="cell-6" colspan="2" rowspan="2"><p>Neon District</p></td>
                        <td id="cell-5" colspan="2" rowspan="2"><p>Cyber Station South</p></td>
                        <td id="cell-3" colspan="2" rowspan="2"><p>Pixel Park</p></td>
                        <td id="cell-1" colspan="2" rowspan="2"><p>Pixel Street</p></td>
                        <td class="schema"></td>
                    </tr>
                    <tr>
                        <td class="schema"></td>
                    </tr>
            </table>
        <!-- Players Container -->

        <div class="game">
            <div class="player selected" id="p1" onclick="showPossession('p1')">
                <div class="info">
                    <p>Giocatore 1</p>
                    <p id="money">99999$</p>
                </div>
                <div class="possession">

                </div>
            </div>
            <div class="player" id="p2" onclick="showPossession('p2')">
                <div class="info">
                    <p>Giocatore 2</p>
                    <p id="money">99999$</p>
                </div>
                <div class="possession">

                </div>
            </div>
            <div class="player" id="p3" onclick="showPossession('p3')">
                <div class="info">
                    <p>Giocatore 3</p>
                    <p id="money">99999$</p>
                </div>
                <div class="possession">

                </div>
            </div>
            <div class="player" id="p4" onclick="showPossession('p4')">
                <div class="info">
                    <p>Giocatore 4</p>
                    <p id="money">99999$</p>
                </div>
                <div class="possession">

                </div>
            </div>



        <div class="action-bar">
            <div id="rettangoloDado1" class="dado" onclick="tiraDadi()"></div>
            <div id="rettangoloDado2" class="dado" onclick="tiraDadi()"></div>
            <div class="button">TRADE</div>
            <div class="button">QUIT</div>
        </div>
        </div>
    </div>
</div>

</body>
</html>