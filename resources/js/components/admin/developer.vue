<template>
    <div id="app" class="container">
        <div class="row mt-5" v-if="!$gate.isAdmin()">
            <not-found></not-found>
        </div>
        <div class="row justify-content-center" v-if="$gate.isAdmin()">
            <div class="col-md-12">
                <passport-clients></passport-clients>
                <passport-authorized-clients></passport-authorized-clients>
                <passport-personal-access-tokens></passport-personal-access-tokens>
            </div>
        </div>
    </div>
</template>
<script></script>
<style></style>