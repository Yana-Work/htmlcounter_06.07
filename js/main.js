document.addEventListener("DOMContentLoaded", function() {
   const form = document.querySelector(".form form");
   const submitBtn = form.querySelector(".button button");
   const errContainer = document.getElementById("errContainer");
   const dataContainer = document.getElementById("dataContainer");

   form.onsubmit = (e) => {
      e.preventDefault();
   };

   submitBtn.addEventListener("click", addData);

   function addData() {
      // Send the form data through fetch to PHP
      let formData = new FormData(form);

      fetch("php/form.php", {
         method: "POST",
         body: formData
      })
         .then((response) => {
            if (!response.ok) {
               throw new Error("Network response was not OK");
            }
            return response.json();
         })
         .then((responseData) => {
            console.log(responseData);
            if (responseData.error) {
               errContainer.style.display = "flex";
               errContainer.textContent = responseData.error;
            } else {
               window.location.href = `result.php?url=${responseData.url}&date=${responseData.date}&responseTime=${responseData.responseTime}&count=${responseData.count}&element=${responseData.element}&domain=${responseData.domain}&totalCheckedURLs=${responseData.totalCheckedURLs}&totalElementCountFromDomain=${responseData.totalElementCountFromDomain}&totalElementCount=${responseData.totalElementCount}&averageFetchTime=${responseData.averageFetchTime}`;
            }
         })
         .catch((error) => {
            console.log(error);
            errContainer.style.display = "flex";
            errContainer.textContent = error.message;
         });
   }
});
