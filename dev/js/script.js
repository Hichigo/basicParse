window.onload = function() {
  var expandBlock, i, j, ref;
  expandBlock = document.getElementsByClassName('expand-js');
  for (i = j = 0, ref = expandBlock.length; 0 <= ref ? j < ref : j > ref; i = 0 <= ref ? ++j : --j) {
    expandBlock[i].onclick = function() {
      this.parentNode.classList.toggle('auto-height');
    };
  }
};
