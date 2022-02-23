/*=================================>> CHARTS  <<=================================*/

(async function main() {
   if (adminRole) {
      displayDatasets();
   }

   const userPseudoSlot = document.querySelector("#user_pseudo");
   const deleteButton = document.querySelector("input[name=delete]");
   
   userPseudoSlot.textContent = userData.pseudo;
   deleteButton.addEventListener("click", async (e) => {
      e.preventDefault();
      
      if (confirm("Are you sure you want to delete your account ?")) {
         const query = await queryControler("userControler", [`type=deleteUser`]);
         if (query.success) {
            logOut();
         } 
      }
   })
})();

async function displayDatasets() {
   const canva = document.querySelector("#chart1");   
   const userCountSlot = document.querySelector("#userCount");
   const userCount = await queryControler("adminControler", [
      `type=getData`,
      `target=userCount`
   ]);

   const range = 48;
   const resolution = "Hourly";
   const loginCount = await queryControler("adminControler", [
      `type=getData`,
      `target=logins`,
      `resolution=${resolution}`,
      `range=${range}`
   ]);
   const regCount = await queryControler("adminControler", [
      `type=getData`,
      `target=register`,
      `resolution=${resolution}`,
      `range=${range}`
   ]);

   userCountSlot.textContent = `The website has ${userCount} registered users !`;
   drawChart(canva, [ loginCount, regCount ], [
      `${resolution} Connexions / Registrations for the last ${range} ${resolution.slice(0, -2)}s`,
      ["Connexions", "Registrations"],
   ]); 
}

function drawChart(canva, datasets, style) {
   const chartList = [];
   const ctx = canva;
   const config = {
      type: 'line',
      data: {
         datasets: [
         {
            label: style[1][0],
            data: datasets[0],  
            normalized: true,
            
            fill: true,
            backgroundColor: 'rgba(169, 185, 118, 0.2)',
            borderColor: 'rgb(169, 185, 118)',
         },
         {
            label: style[1][1],
            data: datasets[1],
            normalized: true,

            fill: true,
            backgroundColor: 'rgba(112, 131, 187, 0.2)',
            borderColor: 'rgb(112, 131, 187)',
         },
      ]},
      options: {
         responsive: false, 
         elements: {
            line: {
               cubicInterpolationMode: "monotone",
               borderWidth: 2.5,
               borderJoinStyle: "round",
            } 
         },
         plugins: {
            title: {
               display: true,
               text: style[0],
               align: "center",
               position: "bottom",
               font: {
                  size: 20,
                  family: 'Viga',
                  weight: 500
               },
               padding: {
                  top: 15,
                  bottom: 30
               }
            },
            legend: {
               display: true,
               labels: {
                  font: {
                     size: 15,
                     family: 'Viga',
                     weight: 300
                  },
                  boxWidth: 60,
                  boxHeight: 20,
               }
            },
            tooltip: {
               padding: 10,
            }
         },
         scales: {
            x : {
               ticks: {
                  maxRotation: 0,
                  maxTicksLimit: 10,
                  padding: 10
               },
            },
            y : {
               ticks: {
                  maxRotation: 0,
                  maxTicksLimit: 10,
                  padding: 10
               },
            }
         }
      },
   }
   const chart = new Chart(ctx, config);
   chartList.push(chart);
}

