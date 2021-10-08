<template>
<el-row :gutter="20">
  <el-col :span="20">
    <el-card class="box-card" shadow="always">
        <div slot="header" class="clearfix">
            <h3>Habitación cargada</h3>
        </div>
        <div>
            <table border class="room-table">
                <tbody>
                    <tr v-for="(line_node, index) in nodes" :key="index">
                        <td :class="node == 1 ? 'wall' : 'empty'" v-for="(node, index_node) in line_node" :key="`${index}-${index_node}`"> {{ node }}</td>
                    </tr>
            </tbody>
            </table>
        </div>
    </el-card>
    <el-card class="box-card" shadow="always" v-if="processedMatrix">
        <div slot="header" class="clearfix">
            <h3>Distribución de bombillos</h3>
            <el-tag size="mini">la letra 'i' en las casillas sin colorear indica que esta iluminada (para cuestiones de verificación rápida)</el-tag>
        </div>
        <div>
            <table border class="room-table">
                <tbody>
                    <tr v-for="(line_node, index) in room" :key="`room-${index}`">
                        <td :class="classNode(node)" v-for="(node, index_node) in line_node" :key="`room-${index}-${index_node}`"> 
                            <i class="el-icon-s-opportunity" v-if="node.isLightBulb"></i>
                            <span v-if="node.illuminated && !node.isWall && !node.isLightBulb">i</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </el-card>
  </el-col>
</el-row>
    
</template>
<script>
    export default {
        props: {
            nodes: {
                required: true,
                default: null,
            },
            room:{
                required: true,
                default: [],
            }
        },
        data () {
            return {
            }
        },
        computed: {
            processedMatrix(){
                return this.room.length > 0;
            }
        },
        methods: {
            classNode(node){
                if(node.isWall){
                    return 'wall';
                }else if(node.isLightBulb){
                    return 'ligthbulb';
                }else return 'empty';
            }
        }
    }
</script>
<style scoped>
.room-table{
    margin: 0 auto;
}
.room-table td{
    text-align: center;
    height: 50px;
    width: 50px !important;
}
.room-table td.wall{
    background-color: #000000;
    color: white;
}
.room-table td.ligthbulb{
    background-color: yellow;
}

</style>