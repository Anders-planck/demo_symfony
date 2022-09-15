import {useCallback, useState} from "react";
import React from "react";

async function jsonLdFecth(url,method="GET",data=  null) {
    const params = {
        method: method,
        headers:{
            "accept": "application/ld+json",
            "content-type": "application/json"
        }
    }
    if(data){
        params.body = JSON.stringify(data);
    }

    const response = await fetch(url,params)
    if(response.status === 204) return null;

    const responseData = await response.json()
    if(response.ok){
        return responseData
    }else{
        throw responseData
    }

}

export  function usePaginatorFetch(url) {
    const [loading, setLoading] = useState(false);
    const [items,setItems] = useState([]);
    const [nextUrl, setNextUrl] = useState(null)
    const [totalItems,setTotalItems] = useState(0)

    const load = useCallback(async ()=> {
        setLoading(true);
        try {
            const response = await jsonLdFecth(nextUrl || url);
            setItems(items => [...items,...response["hydra:member"]])
            if(response["hydra:view"] && response["hydra:view"]["hydra:next"]){
               setNextUrl(response["hydra:view"]["hydra:next"])
            }else{
                setNextUrl(null)
            }
            setTotalItems(response["hydra:totalItems"])
            setLoading(false)
        }catch (e) {
            console.error(e)
        }

    },[url,nextUrl])

    return {
        items,
        setItems,
        load,
        loading,
        totalItems,
        hasMore : nextUrl !== null
    }
}

export function useFecth(url,method="POST",callback=null){
    const [errors, setErrors] = useState({})
    const [loading, setLoading] = useState(false)

    const load = useCallback(async (data = null)=>{
        try {
            setLoading(true)
            const response =  await jsonLdFecth(url,method,data);
            if (callback) callback(response)
        }catch (e) {
            if(e['violations'])
                setErrors(e["violations"].reduce((acc,violations) =>{
                    acc[violations["propertyPath"]] = violations.message
                    return acc
                },{}))
        }finally {
            setLoading(false)
        }
    },[url,method,callback])
    const clearError = useCallback((name)=>{
        if (errors[name])
            setErrors(errors => ({...errors,[name]:null}))
    },[errors])
    return {
        load,
        loading,
        errors,
        clearError
    }
}