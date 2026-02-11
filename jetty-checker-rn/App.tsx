// SIMPLIFIED App.tsx for debugging
import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ActivityIndicator } from 'react-native';
import { StatusBar } from 'expo-status-bar';
import { GestureHandlerRootView } from 'react-native-gesture-handler';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import { Provider } from 'react-redux';
import { store, useAppDispatch, useAppSelector } from './src/store';
import { checkAuthStatus } from './src/store/slices/authSlice';
import { LoginScreen, HomeScreen, QRScannerScreen } from './src/screens';
import { colors } from './src/theme';

const Stack = createStackNavigator();

// Simple Splash Screen
function SplashScreen() {
  return (
    <View style={styles.splashContainer}>
      <Text style={styles.splashTitle}>🎫 Jetty Checker</Text>
      <ActivityIndicator size="large" color="#fff" style={{ marginTop: 20 }} />
      <Text style={styles.splashText}>Loading...</Text>
    </View>
  );
}

// Navigator with auth check
function AppNavigator() {
  const dispatch = useAppDispatch();
  const { isAuthenticated, isCheckingAuth } = useAppSelector((state) => state.auth);
  const [ready, setReady] = useState(false);

  useEffect(() => {
    // Check auth status then mark as ready
    dispatch(checkAuthStatus()).finally(() => {
      setReady(true);
    });
  }, [dispatch]);

  if (!ready || isCheckingAuth) {
    return <SplashScreen />;
  }

  if (isAuthenticated) {
    return (
      <Stack.Navigator screenOptions={{ headerShown: false }}>
        <Stack.Screen name="Home" component={HomeScreen} />
        <Stack.Screen name="QRScanner" component={QRScannerScreen} options={{ presentation: 'modal' }} />
      </Stack.Navigator>
    );
  }

  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      <Stack.Screen name="Login" component={LoginScreen} />
    </Stack.Navigator>
  );
}

export default function App() {
  return (
    <GestureHandlerRootView style={styles.container}>
      <Provider store={store}>
        <NavigationContainer>
          <StatusBar style="light" />
          <AppNavigator />
        </NavigationContainer>
      </Provider>
    </GestureHandlerRootView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  splashContainer: {
    flex: 1,
    backgroundColor: '#0066cc',
    justifyContent: 'center',
    alignItems: 'center',
  },
  splashTitle: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#fff',
  },
  splashText: {
    fontSize: 16,
    color: '#fff',
    marginTop: 10,
    opacity: 0.8,
  },
});
