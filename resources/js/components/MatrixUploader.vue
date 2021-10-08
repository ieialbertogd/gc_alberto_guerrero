<template>
<el-row :gutter="20">
  <el-col :span="20">
    <el-card class="box-card" shadow="always">
        <div slot="header" class="clearfix">
            <h3>Matrix uploader</h3>
            <el-tag size="mini">Por favor seleccione el archivo .txt con su matriz de datos</el-tag>
        </div>
        <div>
            <el-upload
                ref="uploader"
                accept="text/plain"
                :multiple="false"
                action="#"
                :on-remove="onFileRemoved"
                :before-upload="onBeforeUploadFile"
                :before-remove="beforeRemove"
                :file-list="fileList"
                :on-change="changeFileList"
                :auto-upload="false">
                <el-button slot="trigger" size="small" type="primary">Seleccionar archivo</el-button>
                <el-button style="margin-left: 10px;" :disabled="!fileSelected" size="small" type="success" @click="uploadMatrix">Cargar</el-button>
                <div class="el-upload__tip" slot="tip">Solo archivos con extension .txt son válidos</div>
            </el-upload>
        </div>
    </el-card>
  </el-col>
</el-row>
    
</template>
<script>
    export default {
        data () {
            return {
                fileList:[],
                limitFiles: 1,
            }
        },
        computed: {
            fileSelected(){
                return this.fileList.length > 0;
            },
        },
        methods: {
            uploadMatrix(){
                this.$bus.$emit('loading', 'Cargando matriz de datos...');

                const matrixFile = this.$refs.uploader.uploadFiles[0];
                let formData = new FormData();
                formData.append('matrix', matrixFile.raw);
                axios.post(this.$routeLaravel('matrix.upload'), formData, {
                   'Content-type': 'multipart/form-data',
                })
                .then( response => {//Matrix loaded correctly
                    this.$bus.$emit('loadingOff');
                    this.$emit("matrixLoaded", response.data);
                    this.fileList = [];
                }).catch( error => {
                    this.$bus.$emit('loadingOff');
                });
            },
            changeFileList() {
                this.fileList = this.$refs.uploader.uploadFiles
                if(this.fileList.length === 2) this.fileList.splice(0, 1);
            },
            handleExceed(files, fileList) {
                this.$message.warning(`Solo se permiten ${this.limitFiles} archivo a la vez. Si desea cambiarlo, elimine el archivo actual de la lista de archivos seleccionados.`);
            },
            beforeRemove(file, fileList) {
                return this.$confirm(`¿Realmente desea eliminar el archivo ${ file.name }？`);
            },
            onFileRemoved(){
                this.fileList = [];
            },
            onBeforeUploadFile(file){
                const isValid = file.type === 'text/plain'
                const isLt1M = file.size / 1024 / 1024 < 1
                if (!isValid) {
                    this.$message.error('Upload file can only be in txt format!')
                }
                if (!isLt1M) {
                    this.$message.error('Upload file size cannot exceed 1MB!')
                }
                return isValid && isLt1M
            },
        }
    }
</script>
<style scoped>
</style>