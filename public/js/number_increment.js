function animatedValue(id, duration = 2000)
{
    const element = document.getElementById(id);
    const endValue = parseInt(element.textContent, 10);

    function step(currentTime)
    {
        if(!startTime) startTime = currentTime;
        const progress = Math.min((currentTime - startTime) / duration, 1);
        const value = Math.floor(progress * endValue);
        element.textContent = value;

        if(progress < 1)
        {
            requestAnimationFrame(step);
        }
    }

    let startTime = null;
    element.textContent = "0";
    requestAnimationFrame(step);
}

window.addEventListener("DOMContentLoaded", () => 
    {
        animatedValue("visited-value")
        animatedValue("countries-value")
        animatedValue("active-value")
        animatedValue("inactive-value")
    });