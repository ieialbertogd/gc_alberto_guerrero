import Welcome from "../components/Welcome";
import MatrixUploader from "../components/MatrixUploader";


const prefix = "/main";

export default {
    routes: [
        {
            path: prefix + "/welcome",
            name: "welcome",
            component: Welcome
        },
        {
            path: prefix + "/gc_logictest",
            name: "gc_logictest",
            component: MatrixUploader
        },
        {
            path: prefix + "/result",
            name: "result",
            component: Welcome
        },
    ],
    
};