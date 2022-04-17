<template>
	<div class="container">
		<div class="row">
			<div class="col-sm-6 offset-sm-3">
                <div class="alert alert-success mt-1" v-bind:class="{'alert-danger':hasError,'d-none':!hasMessage}">
                    <strong>{{status}}!</strong> {{message}}
                </div>
				<div class="card mt-3">
	                <div class="card-header">
	                    <h4>Create Short URL</h4>
	                </div>
	                <div class="card-body">
	                    <form @submit.prevent="create">
	                        <div class="row">
	                            <div class="col-12 mb-2">
	                                <div class="form-group">
	                                    <label>Long URL</label>
	                                    <input type="text" class="form-control" v-model="shortner.long_url">
	                                </div>
	                            </div>
	                            <div class="col-12">
	                                <button type="submit" class="btn btn-primary">Save</button>
	                            </div>
	                        </div>
	                    </form>
	                </div>
	            </div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	import axios from 'axios';
	export default{
		name:"App",
		data(){
			return {
				shortner:{
					long_url:"",
				},
                hasError:false,
                status:"Success",
                hasMessage:false,
                message:"",

			}
		},
		methods:{
			async create(){
				await axios.post('/api/shortner',this.shortner).then(response=>{
					console.log(response);
                    this.hasError = false
                    this.hasMessage = true
                    this.status = "Success"
                    this.message = response.data.message
                    this.shortner.long_url=""
				}).catch(error => {
                    this.hasError = true
                    this.hasMessage = true
                    this.status = "Failed"
                    this.message = error.response.data.message
				})
			}
		},
	}
</script>
