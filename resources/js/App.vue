<template>
<el-container style="height: 100vh; border: 0px solid red">
  <el-aside width="250px" style="background-color: rgb(238, 241, 246);" class="asidefull">
    <el-menu
      default-active="1"
      class="el-menu-vertical"
      background-color="#4DA936"
      text-color="#fff"
      active-text-color="#ffd04b">
      <el-menu-item index="1" @click="setAction(1)">
        <i class="el-icon-s-grid"></i>
        <span>Cargar Habitación</span>
      </el-menu-item>
      <el-menu-item index="2" @click="setAction(2)" :disabled="matrixEmpty">
        <i class="el-icon-s-opportunity"></i>
        <span>Mostrar bombillos</span>
      </el-menu-item>
    </el-menu>
  </el-aside>

  <el-container>
<el-header><h4>PRUEBA DE LÓGICA GC - ALBERTO GUERRERO DURAN</h4></el-header>
    <el-main  class="bg-purple-light">
        <div v-if="loadMatrix && matrixEmpty">
            <matrix-uploader @matrixLoaded = drawMatrix />
        </div>
        <div v-if="!matrixEmpty">
            <room-drawer :nodes="matrixData" :room="analyzedMatrix"></room-drawer>
        </div>
    </el-main>
  </el-container>
</el-container>

</template>

<script>
import Vue from 'vue';
import VueRouter from 'vue-router'
import lang from 'element-ui/lib/locale/lang/es'
import locale from 'element-ui/lib/locale'

// configure language
locale.use(lang)

import {
    Container,
    Header,
    Aside,
    Main,
    Dropdown,
    Button
} from 'element-ui';

import '../sass/element-variables.scss'

Vue.use(VueRouter)
Vue.use(Container)
Vue.use(Header)
Vue.use(Aside)
Vue.use(Main)
Vue.use(Dropdown)
Vue.use(Button)

//import routes configuration
import config from './router/config.js'
import MatrixUploader from './components/MatrixUploader.vue';
import RoomDrawer from './components/RoomDrawer.vue';

const router = new VueRouter({
    mode: 'history',
    routes: config.routes,
});

 export default {
        components: { 
            MatrixUploader,
            RoomDrawer
        },
         router: router,
         data() {
             return {
                 loading: null,
                 loadMatrix: true,
                 matrixData: [],
                 analyzedMatrix: [],
             }
         },
         computed: {
             matrixEmpty() {
                 return this.matrixData.length <= 0;
             }
         },
        methods: {
            loadAppListeners () {
                this.$bus.$off('loading');
                this.$bus.$off('loadingOff');
                this.$bus.$on('loading', (text) => {
                    this.loading = this.$loading({
                    lock: true,
                    text: text || '',
                    spinner: 'el-icon-loading',
                    background: 'rgba(0, 0, 0, 0.7)'
                    });
                });

                this.$bus.$on('loadingOff', () => {
                    this.loading.close();
                });
            },
            drawMatrix(matrixLoaded){
                this.matrixData = matrixLoaded;
            },
            setAction(action){
                if(action == 1) {
                    this.loadMatrix = true;
                    this.matrixData = [];
                    this.analyzedMatrix = [];
                } else {
                    //go to calc number of light bulbs
                    this.calcMinimumLightBulbs();
                }
            },
            calcMinimumLightBulbs(){
                this.$bus.$emit('loading', 'Calculando número minimo de bombillos...');
                axios.post(this.$routeLaravel('matrix.analyze'), {room: this.matrixData})
                 .then((response) => {
                    this.$bus.$emit('loadingOff');
                    console.log(response.data);
                    this.analyzedMatrix = response.data;
                 }).catch((err) => {
                    this.$bus.$emit('loadingOff');

                    this.$message({
                        type: 'warning',
                        message: 'Error al analizar los datos'
                    });
                 });
            },
            openView(_nameView) {
                this.$router.push(_nameView)
            },
        },
        created(){
            this.loadAppListeners();
            this.$router.push(window.Laravel.route_base);
         }
 }
</script>
<style>
.el-icon-loading{
    font-size: 30px !important;
}
.el-menu {
      height: 99.5vh !important;
      text-transform: uppercase;
  }

  .el-menu-item {
      font-size: .8em !important;
      color: #788EB5;
      font-weight: 700;
      letter-spacing: 1px
  }
  .el-menu-item i{
      color: #FFFFFF;
  }
</style>
