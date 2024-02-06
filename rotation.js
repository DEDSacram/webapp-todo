let rotation = [
  {src: "./img/logo1.svg", name: "Vortex Vision"},
  {src: "./img/logo2.svg", name: "Echostream"},
  {src: "./img/logo3.svg", name: "Saurkraut Corporation"},
  {src: "./img/logo4.svg", name: "Schniwetzel"},
  {src: "./img/logo5.svg", name: "Questionarre"},
  {src: "./img/logo6.svg", name: "Nimbus Dynamics"},
  {src: "./img/logo7.svg", name: "Panthen Inc."}
];

let placements = document.querySelectorAll('.moving-image');
placements.forEach((placement, i) => {
  let img = document.createElement('img');
  img.src = rotation[i].src;
  let title = document.createElement('h3');
  title.textContent = rotation[i].name;
  placement.append(img);
  placement.append(title);
});

let startTime = null;

let equalspacing = 10000; // Adjust this value to your liking

function animate(timestamp) {
  if (!startTime) startTime = timestamp;
  let elapsedTime = timestamp - startTime;

  placements.forEach((placement, i) => {
    let progress = (elapsedTime / equalspacing - i / placements.length) % 1;
    let x = window.innerWidth - progress * (window.innerWidth + placement.offsetWidth);
    placement.style.transform = `translateX(${x}px)`;

    // Calculate opacity based on x position
    let fadeInOutZone = window.innerWidth * 0.1;
    let opacity;
    if (x < fadeInOutZone) {
      opacity = x / fadeInOutZone;
    } else if (x > window.innerWidth - fadeInOutZone) {
      opacity = (window.innerWidth - x) / fadeInOutZone;
    } else {
      opacity = 1;
    }
    placement.style.opacity = opacity;
  });

  requestAnimationFrame(animate);
}

requestAnimationFrame(animate);0