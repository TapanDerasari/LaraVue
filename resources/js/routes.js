import Vue from 'vue'
import Router from 'vue-router'
// import home from './components/Home'
import blog from './components/blog'
import about from './components/about'
import contact from './components/contact'
//admin imports
import dashboard from './components/admin/dashboard'
import developer from './components/admin/developer'
import profile from './components/admin/profile'
import task from './components/task'
import user from './components/admin/user'
import notfound from './components/notfound'

Vue.use(Router)

export default new Router({
    // mode: 'history',
    linkActiveClass: 'active',
    routes: [
        {path: '/blog', name: 'blog', component: blog},

        {path: '/about', name: 'about', component: about},

        {path: '/contact', name: 'contact', component: contact},
        //Admin routes
        {path: '/profile', name: 'profile', component: profile},

        {path: '/dashboard', name: 'dashboard', component: dashboard},

        {path: '/developer', name: 'developer', component: developer},

        {path: '/task', name: 'task', component: task},

        {path: '/users', name: 'users', component: user},

        {path: '*', name: 'users', component: notfound}
    ]
})