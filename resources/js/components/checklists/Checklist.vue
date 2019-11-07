<template>
    <div>
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{edit_checklist ? 'Update Checklist' : 'New Checklist'}}</h3>
                </div>
                <div class="box-body">
                    <form @submit.prevent="saveChecklist()" id="checklist">
                      <div class="form-group">
                          <label>Name</label>
                          <input type="text" v-model="form.name" class="form-control" required>
                      </div>
                        <div class="form-group">
                            <label>Expiry Type</label>
                            <select name="expiry_type_id" class="form-control" v-model="form.expiry_type_id" required @change="getDuration()">
                                <option :value="expiry.id" v-for="expiry in expiry_types" :key="expiry.id">{{expiry.name}}</option>
                            </select>
                             </div>
                        <div class="form-group" v-if="show_duration">
                            <label>Enter Number of {{duration_type}}</label>
                            <input type="number" class="form-control" v-model="form.duration_no" required>
                        </div>
                        <div class="form-group">
                            <label class="radio-inline"><input type="radio" name="check_list" value="0"
                                                               v-model="check_list">Type Checklist manually</label>
                            <label class="radio-inline"><input type="radio" name="check_list"
                                                               value="1" v-model="check_list">Upload File</label>
                        </div>
                             <div class="form-group" v-if="check_list==1">
                            <label>Upload Checklist</label>
                            <input type="file" class="form-control" @change="onFileChange">
                        </div>
                        <div class="form-group" v-if="check_list==0">
                            <label>Enter Checklist</label>
                            <table>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr v-for="(checklist,k) in form.checklists">
                                    <td><input type="text" class="form-control" placeholder="Action Name" v-model="checklist.name"></td>
                                    <td><textarea class="form-control description" placeholder="Description" v-model="checklist.description"></textarea></td>
                                    <td>
                                        <i class="fa fa-minus-circle add" @click="removeChecklist(k)" v-show="k || (!k && form.checklists.length > 1)"></i>
                                        <i class="fa fa-plus-circle add" @click="addChecklist(k)" v-show="k == form.checklists.length-1"></i>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary">{{edit_checklist ? 'Update' : 'Save'}}</button>
                        <button type="button" class="btn btn-outline-danger" @click="cancel">Cancel</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</template>

<script>

    export default {
        props:['edit'],
        data(){
            return {
                form:{
                    expiry_type_id:'',
                    duration_no:'',
                    checklist_file:'',
                    name:'',
                    checklists:[{name:'',description:''}],
                    id:''
                },
                edit_checklist: this.edit,
                expiry_types:{},
                duration_type:'',
                show_duration:false,
                check_list:0
            }
        },
        created(){
            this.listen();
            this.getExpiryTypes();
        },
        methods:{
              removeChecklist(i){
              this.form.checklists.splice(i,1);
            },
            addChecklist(){
              this.form.checklists.push({name:'',description:''})
            },
            getDuration(){
                this.expiry_types.forEach(type => {
                    if (type.id === this.form.expiry_type_id){
                        this.duration_type = type.name;
                        this.show_duration = true;
                        return;
                    }
                })
            },
            getExpiryTypes(){
                axios.get('expiry-types')
                    .then(expiry => {
                        this.expiry_types = expiry.data;
                    })
            },
            onFileChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);
            },
            createImage(file) {
                let reader = new FileReader();
                let vm = this;
                reader.onload = (e) => {
                    vm.form.checklist_file = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            saveChecklist(){
                if (this.check_list ===0){
                   if (Object.values(this.form.checklists[0])[0] === '' ||  Object.values(this.form.checklists[0])[0] === ''){
                       return this.$toastr.e('Checklist field are required.')
                   }
                    for (let i = 0; i < this.form.checklists.length; i++){
                        if((this.form.checklists[i]['name'] ===null || this.form.checklists[i]['name'] ==='') || (this.form.checklists[i]['description'] ===null || this.form.checklists[i]['description'] ==='')){
                            return this.$toastr.e('All checklist fields are required');
                        }
                    }
                }
                   else if (this.form.checklist_file ===''){
                       return this.$toastr.e('Checklist file field is required.');
                   }

               this.edit_checklist ? this.update() : this.save();
            },
            save(){

                let form = document.getElementById('checklist');
                let formData = new FormData(form);
                formData.append('checklist_file',this.form.checklist_file);
                formData.append('expiry_type_id',this.form.expiry_type_id);
                formData.append('duration_no',this.form.duration_no);
                formData.append('name',this.form.name);
                formData.append('checklists',JSON.stringify(this.form.checklists));
                const config = {
                    headers: {'Content-Type': 'multipart/form-data'}
                }
                delete this.form.id;
                axios.post('checklists',formData,config)
                    .then(res => eventBus.$emit('listChecklists',res.data))
                    .catch(error => error.response)
            },
            update(){
                let form = document.getElementById('checklist');
                let formData = new FormData(form);
                formData.append('checklist_file',this.form.checklist_file);
                formData.append('expiry_type_id',this.form.expiry_type_id);
                formData.append('duration_no',this.form.duration_no);
                formData.append('name',this.form.name);
                formData.append('checklists',JSON.stringify(this.form.checklists));
                formData.append('_method','PUT');
                const config = {
                    headers: {'Content-Type': 'multipart/form-data'}
                }
                axios.post(`checklists/${this.form.id}`,formData,config)
                    .then(res => {
                         this.edit_checklist = false;
                         eventBus.$emit('updateChecklist',res.data);
                    })
                    .catch(error => error.response)
            },
            convertDate(str) {
                var date = new Date(str),
                    mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                    day = ("0" + date.getDate()).slice(-2);
                return [date.getFullYear(), mnth, day].join("-");
            },
            cancel(){
                eventBus.$emit('cancel')
            },
            listen(){
                if (this.edit){
                    this.form = this.$store.state.checklists
                    this.show_duration = true;
                    this.form.checklists = JSON.parse(this.$store.state.checklists.checklists)
                    if(this.form.checklist_file ==='' || this.form.checklist_file ===null){
                        this.check_list =0;
                    }else {
                        this.check_list =1;
                    }

                }
               },
        }
    }
</script>

<style scoped>
.add{
    margin-left: 25px;
    font-size: 18px;
}
    .description{
        margin-left: 20px;
        margin-top: 15px;

    }
</style>
