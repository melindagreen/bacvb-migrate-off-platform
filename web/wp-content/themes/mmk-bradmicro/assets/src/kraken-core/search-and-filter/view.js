// alter filter bar dom structure
function filterBarFlex() {
    const filterBar = document.querySelector('.wp-block-kraken-core-search-and-filter .filter-bar');
    const childrenMinusSearch = filterBar.querySelectorAll(':scope > *:not(.filter-search)');
    const wrapper = document.createElement('div');
    wrapper.classList.add("filters-wrapper")
    
    filterBar.append(wrapper);
    wrapper.replaceChildren(...childrenMinusSearch);
}
filterBarFlex();