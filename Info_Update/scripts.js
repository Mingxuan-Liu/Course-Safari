document.addEventListener("DOMContentLoaded", function () {
    const listItems = document.querySelectorAll("#toggled-list-items li");
  
    listItems.forEach(function (item) {
      item.addEventListener("click", function () {
        handleListItemClick(this, listItems);
      });
    });
  });
  
  function handleListItemClick(clickedItem, allItems) {
    const itemText = clickedItem.textContent;
    console.log("Clicked item: " + itemText);
  
    // Reset the background color of all items
    allItems.forEach(function (item) {
      item.style.backgroundColor = "";
    });
  
    // Set the background color of the clicked item to gray
    clickedItem.style.backgroundColor = "gray";
  
    // Create a custom event to pass the clicked item's information
    const itemClickedEvent = new CustomEvent("itemClicked", {
      detail: { text: itemText },
    });
  
    // Dispatch the custom event
    document.dispatchEvent(itemClickedEvent);
  }