<template>
	<div class="errorlist">
		<h4>Check Grammar Result</h4><hr>

	    <table>
	        <tbody>
	            <tr v-for="worse in grammar.errors">
	                <td><span class="red">{{ worse.bad }}</span></td>
	                <td style="padding: 0 2em 0 1.5em;">→</td>
	                <td>
	                	<div v-for="good in worse.better">
		                	<span class="green">{{ good }}</span>
		                	<br>
		                </div>
	                </td>
	            </tr>
	        </tbody>
	    </table>

	    <br>
		<h5>Score: <b>{{ grammar.score }}</b></h5>
	</div>
</template>

<script>
	export default {
		props: ['grammar']
	}
</script>

<style scoped>
	.errorlist {
		background: #ffedf0;
	    padding: .5em 1em;
	    margin: 0 0 2em;
	    border: 1px solid #f9d8dd;
	}

	table { 
		width: 100%; 
		font-family: tahoma;
		font-weight: normal;
		font-size: 1.1em;
		line-height: 1.6em;
		color: #B52E47;
	}

	table td { vertical-align: top; }
	td:nth-child(3) { word-break: break-word; }
	table td span.green { 
		padding-right: .5em;  
		white-space: pre-wrap;
	}

	.errorlist .red { color: #B52E47; }
	.errorlist .green { color: #3FBD44; }

	hr { border-top: 1px solid #c5c5c5; }
</style>