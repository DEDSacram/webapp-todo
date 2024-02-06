// possible cleanup

// add callback after confirm

// need to move mouse to pick color on sidepanel also wheel

//add responsivity

// doesnt count with any margins or padding

function degreesToRadians(degrees) {
    return degrees * (Math.PI / 180);
  }

  
  function rgbToHex(color){
    if (color.r > 255 || color.g > 255 || color.b > 255)
        throw "Invalid color component";
    return ((color.r << 16) | (color.g << 8) | color.b).toString(16);
    }


function createcolorpicker(canvasname,brightnessname,previewname){
    const canvas = document.getElementById(canvasname);
    const brightness = document.getElementById(brightnessname);
    const preview = document.getElementById(previewname);

    drawColorWheel(canvas, 150);
    createsidepanel(brightness,preview)
    wheelinteractivity(canvas,preview,brightness)
}


  function wheelinteractivity(canvas,previewer,brightness){
      // save allocation by the possibility of not picking a color only previewing events
  canvas.addEventListener("mousedown", (event) => {
    // instead of document listener can be fixed with a draggable div
    
    // let canvas = event.target;

    // checking the circle
    let rect = canvas.getBoundingClientRect();
    let r = rect.width/2

    let color = []
 
    
    // tags for better performance
    let context = canvas.getContext('2d',{
        antialias: false,
        depth: false,
        desynchronized: true,
        willReadFrequently:true
      });
    function logKey(e) {
      
        if(Math.pow((e.offsetX-r),2) + Math.pow((e.offsetY-r),2) < Math.pow(r,2)){
            color = [...(context.getImageData(e.offsetX, e.offsetY, 1, 1,).data).slice(0,-1)]
            previewer.style.backgroundColor = "rgb("+ color +")"
            brightness.style.background =  "linear-gradient(0, rgb(0,0,0) 0%, rgb("+ color +") 100%)"
        }
    }
    canvas.addEventListener("mousemove", logKey);
    document.addEventListener("mouseup", function handler() {
        canvas.removeEventListener("mousemove",logKey);
        document.removeEventListener("mouseup",handler);
        // var r = document.querySelector(':root');
        // r.style.setProperty('--bs-background', previewer.style.backgroundColor);
        let previewer = document.getElementById('colorpreview');
        previewer.setAttribute('colorstore',color)
        previewer.setAttribute('colorsave',color)
      });
  });
  }

  function createsidepanel(brightness,previewer){

  brightness.addEventListener("mousedown", (event) => {
    let slider = canvas.getBoundingClientRect();
    let rgb = previewer.getAttribute('colorsave').split(',')
    let brightvalue = 1
    function log(e) {
        brightvalue = Math.floor((1-(e.offsetY/slider.height)) * 100) / 100
        previewer.style.backgroundColor =  `rgb(${rgb[0]*brightvalue},${rgb[1]*brightvalue},${rgb[2]*brightvalue},1)`
    }
    brightness.addEventListener("mousemove", log);
    document.addEventListener("mouseup", function handler() {
            brightness.removeEventListener("mousemove",log);
            document.removeEventListener("mouseup",handler);
            previewer.setAttribute('colorstore',`${rgb[0]*brightvalue},${rgb[1]*brightvalue},${rgb[2]*brightvalue}`)
      });
  })
}

  function drawColorWheel(canvas, size = 150) {
    const context = canvas.getContext('2d');
    canvas.width = size;
    canvas.height = size;
  
    const centerColor = 'white';
    // Initiate variables
    let angle = 0;
    const hexCode = [0, 0, 255];
    let pivotPointer = 0;
    const colorOffsetByDegree = 4.322;
    const radius = size / 2;
  
    // For each degree in circle, perform operation
    while (angle < 360) {
      // find index immediately before and after our pivot
      const pivotPointerbefore = (pivotPointer + 3 - 1) % 3;
  
      // Modify colors
      if (hexCode[pivotPointer] < 255) {
        // If main points isn't full, add to main pointer
        hexCode[pivotPointer] =
          hexCode[pivotPointer] + colorOffsetByDegree > 255 ?
          255 :
          hexCode[pivotPointer] + colorOffsetByDegree;
      } else if (hexCode[pivotPointerbefore] > 0) {
        // If color before main isn't zero, subtract
        hexCode[pivotPointerbefore] =
          hexCode[pivotPointerbefore] > colorOffsetByDegree ?
          hexCode[pivotPointerbefore] - colorOffsetByDegree :
          0;
      } else if (hexCode[pivotPointer] >= 255) {
        // If main color is full, move pivot
        hexCode[pivotPointer] = 255;
        pivotPointer = (pivotPointer + 1) % 3;
      }
  
      const rgb = `rgb(${hexCode.map(h => Math.floor(h)).join(',')})`;
      const grad = context.createRadialGradient(
        radius,
        radius,
        0,
        radius,
        radius,
        radius
      );
      grad.addColorStop(0, centerColor);
      grad.addColorStop(1, rgb);
      context.fillStyle = grad;
  
      // draw circle portion
      context.globalCompositeOperation = 'source-over';
      context.beginPath();
      context.moveTo(radius, radius);
      context.arc(
        radius,
        radius,
        radius,
        degreesToRadians(angle),
        degreesToRadians(360)
      );
      context.closePath();
      context.fill();
      angle++;
    }

  }