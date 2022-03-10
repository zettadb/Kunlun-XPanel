<template>
  <div class="components-container board">
    <div class="board-column">
      <div class="board-column-header">cluster1</div>
      <div class="board-column-content">
        <div class="board-item">计算节点（2）</div>
        <div class="board-item">shard（3）</div>
      </div>
    </div>
    <div class="board-column">
      <div class="board-column-header">cluster2</div>
      <div class="board-column-content">
        <div class="board-item">计算节点（2）</div>
        <div class="board-item">shard（3）</div>
      </div>
    </div>
    <div class="board-column">
      <div class="board-column-header">cluster3</div>
      <div class="board-column-content">
        <div class="board-item">计算节点（2）</div>
        <div class="board-item">shard（3）</div>
      </div>
    </div>
  </div>
</template>
<style>
.components-container {
    margin: 30px 50px;
    position: relative;
}
.board {
    /* width: 98%; */
    margin-left: 20px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-pack: distribute;
    justify-content: space-around;
    -webkit-box-orient: horizontal;
    -webkit-box-direction: normal;
    -ms-flex-direction: row;
    flex-direction: row;
    -webkit-box-align: start;
    -ms-flex-align: start;
    align-items: flex-start;
}
.board-column{
    min-width: 300px;
    /* min-width: 33%; */
    min-height: 100px;
    height: auto;
    overflow: hidden;
    background: #f0f0f0;
    border-radius: 3px;
}
.board-column-header{
  background: #4A9FF9;
  height: 50px;
    line-height: 50px;
    overflow: hidden;
    padding: 0 20px;
    text-align: center;
    color: #fff;
    border-radius: 3px 3px 0 0;
    font-size: 20px;
}
.board-column-content {
    height: auto;
    overflow: hidden;
    border: 10px solid transparent;
    min-height: 60px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: start;
    -ms-flex-pack: start;
    justify-content: flex-start;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    font-size: 16px;
}
.board-item{
    cursor: pointer;
    width: 100%;
    height: 128px;
    margin: 5px 0;
    background-color: #fff;
    text-align: center;
    line-height: 54px;
    padding: 5px 10px;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    -webkit-box-shadow: 0px 1px 3px 0 rgb(0 0 0 / 20%);
    box-shadow: 0px 1px 3px 0 rgb(0 0 0 / 20%);
}
</style>