define(["jquery", "theme_eadtraining/trianglify"], function ($, trianglify) {
    return {
        generateimage: function (imageid, courseid, animate) {
            courseid = Math.pow((courseid + 10000), 2);
            const $img = $(`img[src*="${imageid}"],[style*="${imageid}"]`);

            if (!$img.length) {
                return;
            }

            const newImageHeight = $img.height();
            const newImageWidth = $img.width();

            // set up the base pattern
            const pattern = trianglify({
                height: newImageHeight,
                width: newImageWidth,
                xColors: ["rgba(255, 255, 255, .6)"],
                yColors: "match",
                cellSize: 25,
                colorFunction: trianglify.colorFunctions.shadows(),
            });
            let svg = pattern.toSVG();

            function createRandom(seed) {
                let x = Math.sin(seed);
                return Math.abs(x - Math.floor(x));
            }

            function randomColor(sum) {
                const h = 0.1 + createRandom(courseid + sum + 1); // Hue: 0.1 a 1.0
                const s = 0.8 + createRandom(courseid + sum + 2) * 0.2; // Saturação: 80% a 100%
                const v = 0.8 + createRandom(courseid + sum + 3) * 0.2; // Brilho: 80% a 100%

                // Função para converter HSV para RGB
                const hsvParaRgb = (h, s, v) => {
                    let r, g, b;
                    let i = Math.floor(h * 6);
                    let f = h * 6 - i;
                    let p = v * (1 - s);
                    let q = v * (1 - f * s);
                    let t = v * (1 - (1 - f) * s);
                    switch (i % 6) {
                        case 0:
                            r = v, g = t, b = p;
                            break;
                        case 1:
                            r = q, g = v, b = p;
                            break;
                        case 2:
                            r = p, g = v, b = t;
                            break;
                        case 3:
                            r = p, g = q, b = v;
                            break;
                        case 4:
                            r = t, g = p, b = v;
                            break;
                        case 5:
                            r = v, g = p, b = q;
                            break;
                    }
                    return {
                        r: Math.round(r * 255),
                        g: Math.round(g * 255),
                        b: Math.round(b * 255)
                    };
                };

                const {r, g, b} = hsvParaRgb(h, s, v);
                return `rgb(${r}, ${g}, ${b})`;
            }

            function randon(sum, min, max) {
                return Math.floor(createRandom(courseid + sum) * (max - min + 1)) + min;
            }

            let extraSvg = "";
            if (animate) {
                extraSvg = `
                    <style>
                        #color-stop-1 {
                            -webkit-animation: change-color-1 ${randon(0, 15, 40)}s ease-in-out infinite alternate;
                            animation: change-color-1 ${randon(0, 15, 40)}s ease-in-out infinite alternate
                        }
                        #color-stop-2 {
                            -webkit-animation: change-color-2 ${randon(0, 15, 40)}s ease-in-out infinite alternate;
                            animation: change-color-2 ${randon(0, 15, 40)}s ease-in-out infinite alternate
                        }
                        #color-stop-3 {
                            -webkit-animation: change-color-3 ${randon(0, 15, 40)}s ease-in-out infinite alternate;
                            animation: change-color-3 ${randon(0, 15, 40)}s ease-in-out infinite alternate
                        }
                        @keyframes change-color-1 {
                             ${randon(20, 0, 100)}% { stop-color: ${randomColor(10)} }
                             ${randon(21, 0, 100)}% { stop-color: ${randomColor(11)} }
                             ${randon(22, 0, 100)}% { stop-color: ${randomColor(12)} }
                             ${randon(23, 0, 100)}% { stop-color: ${randomColor(13)} }
                        }
                        @keyframes change-color-2 {
                             ${randon(24, 0, 100)}% { stop-color: ${randomColor(14)} }
                             ${randon(25, 0, 100)}% { stop-color: ${randomColor(15)} }
                             ${randon(26, 0, 100)}% { stop-color: ${randomColor(16)} }
                             ${randon(27, 0, 100)}% { stop-color: ${randomColor(17)} }
                        }
                        @keyframes change-color-3 {
                             ${randon(28, 0, 100)}% { stop-color: ${randomColor(18)} }
                             ${randon(29, 0, 100)}% { stop-color: ${randomColor(19)} }
                             ${randon(30, 0, 100)}% { stop-color: ${randomColor(20)} }
                             ${randon(31, 0, 100)}% { stop-color: ${randomColor(21)} }
                        }
                    </style>`;
            }
            extraSvg += `
                <defs>
                    <linearGradient x1="0%" y1="0%" y2="${randon(3, 60, 140)}%" id="url-${courseid}">
                        <stop id="color-stop-1" stop-color="${randomColor(1)}" offset="0%"></stop>
                        <stop id="color-stop-2" stop-color="${randomColor(2)}" offset="${randon(2, 25, 75)}%"></stop>
                        <stop id="color-stop-3" stop-color="${randomColor(3)}" offset="100%"></stop>
                    </linearGradient>
                </defs>
                <path fill="url(#url-${courseid})" d="M0 0h${newImageWidth}v${newImageHeight}H0z"></path>\n`;

            let svgString = new XMLSerializer().serializeToString(svg);
            svgString = svgString.replace(/<svg[^>]*?>/, match => `${match}\n${extraSvg}`);
            const base64 = btoa(unescape(encodeURIComponent(svgString)));
            if ($img.is("img")) {
                $img.attr("src", `data:image/svg+xml;base64,${base64}`) ;
            } else {
                $img.css({"background-image": `url("data:image/svg+xml;base64,${base64}")`})
            }
        },
    };
});
