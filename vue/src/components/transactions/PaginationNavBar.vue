<template>
    <div>
        <ul  class="pagination">
            
                <template v-if="pageCount>13">
                    <li class="page-item" v-for="p in 10" :key="p.id"> <a class="page-link" href="#" @click.prevent="changePage(p)"> {{p}} </a></li>
                    <li class="page-item" > <a class="page-link" href="#"> ... </a></li>
                    <li class="page-item" > <a class="page-link" href="#" @click.prevent="changePage(pageCount-1)"> {{pageCount-1}} </a></li>
                    <li class="page-item" > <a class="page-link" href="#" @click.prevent="changePage(pageCount)"> {{pageCount}} </a></li>
                </template>
                <template v-else>
                    <template v-for="p in pageCount" :key="p.id" >
                        <li class="page-item" > <a class="page-link" href="#" @click.prevent="changePage(p)"> {{p}} </a></li>
                    </template>
                </template>
            
        </ul>
    </div>
</template>
<script>
export default {
    name: "PaginationNavBar",
    props:{
        size:{
            type:Number,
            required:false,
            default: 10
        },
        pageCount:{
            type: Number,
            required:true
        },     
        
    },
    emits: [
        'currentPageNumber',
    ],
    data(){
        return {
            pageNumber: 0  // default to page 1
        }
    },
    
    methods:{
      nextPage(){
         this.pageNumber++;
      },
      prevPage(){
        this.pageNumber--;
      },
      changePage(pageNumber){
        this.pageNumber = pageNumber;
        this.$emit('currentPageNumber', pageNumber);
      }
    }
}
</script>