setInterval(()=>{
  fetch('api_pedidos.php')
    .then(r=>r.json())
    .then(data=>{
      if(data.length>0){
        data.forEach(p=>{
          Swal.fire({
            title: 'Nuevo Pedido!',
            text: p.cliente + " pidió " + p.plato,
            icon: 'info',
            timer: 4000
          });
          let msg = new SpeechSynthesisUtterance("Nuevo pedido de " + p.plato);
          speechSynthesis.speak(msg);
        });
      }
    });
}, 3000);
