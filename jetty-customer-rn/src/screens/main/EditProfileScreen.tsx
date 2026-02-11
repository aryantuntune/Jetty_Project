// Edit Profile Screen following the migration guide
import React, { useState } from 'react';
import {
    View,
    StyleSheet,
    ScrollView,
    TouchableOpacity,
    Alert,
    ActionSheetIOS,
    Platform,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { EditProfileScreenNavigationProp } from '@/types/navigation';
import { colors, spacing } from '@/theme';
import { Button, Input, Avatar } from '@/components/common';
import { useAppSelector, useAppDispatch } from '@/store';
import { updateProfile, uploadProfilePicture } from '@/store/slices/authSlice';
import { getRequiredError, getMobileError } from '@/utils/validators';
import * as ImagePicker from 'expo-image-picker';

export const EditProfileScreen: React.FC = () => {
    const navigation = useNavigation<EditProfileScreenNavigationProp>();
    const dispatch = useAppDispatch();
    const { customer, isLoading } = useAppSelector((state) => state.auth);

    const [firstName, setFirstName] = useState(customer?.firstName || '');
    const [lastName, setLastName] = useState(customer?.lastName || '');
    const [mobile, setMobile] = useState(customer?.mobile || '');
    const [selectedImage, setSelectedImage] = useState<string | null>(null);
    const [errors, setErrors] = useState<Record<string, string | null>>({});

    const showImageOptions = () => {
        if (Platform.OS === 'ios') {
            ActionSheetIOS.showActionSheetWithOptions(
                {
                    options: ['Cancel', 'Take Photo', 'Choose from Library', 'Remove Photo'],
                    cancelButtonIndex: 0,
                    destructiveButtonIndex: 3,
                },
                async (buttonIndex) => {
                    if (buttonIndex === 1) {
                        await takePhoto();
                    } else if (buttonIndex === 2) {
                        await pickImage();
                    } else if (buttonIndex === 3) {
                        setSelectedImage(null);
                    }
                }
            );
        } else {
            Alert.alert(
                'Change Photo',
                'Choose an option',
                [
                    { text: 'Cancel', style: 'cancel' },
                    { text: 'Take Photo', onPress: takePhoto },
                    { text: 'Choose from Library', onPress: pickImage },
                    { text: 'Remove Photo', onPress: () => setSelectedImage(null), style: 'destructive' },
                ]
            );
        }
    };

    const takePhoto = async () => {
        const { status } = await ImagePicker.requestCameraPermissionsAsync();
        if (status !== 'granted') {
            Alert.alert('Permission Denied', 'Camera permission is required to take photos.');
            return;
        }

        const result = await ImagePicker.launchCameraAsync({
            mediaTypes: ImagePicker.MediaTypeOptions.Images,
            allowsEditing: true,
            aspect: [1, 1],
            quality: 0.8,
        });

        if (!result.canceled && result.assets[0]) {
            setSelectedImage(result.assets[0].uri);
        }
    };

    const pickImage = async () => {
        const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
        if (status !== 'granted') {
            Alert.alert('Permission Denied', 'Photo library permission is required to select photos.');
            return;
        }

        const result = await ImagePicker.launchImageLibraryAsync({
            mediaTypes: ImagePicker.MediaTypeOptions.Images,
            allowsEditing: true,
            aspect: [1, 1],
            quality: 0.8,
        });

        if (!result.canceled && result.assets[0]) {
            setSelectedImage(result.assets[0].uri);
        }
    };

    const validateForm = (): boolean => {
        const newErrors = {
            firstName: getRequiredError(firstName, 'First name'),
            lastName: getRequiredError(lastName, 'Last name'),
            mobile: getMobileError(mobile),
        };

        setErrors(newErrors);
        return Object.values(newErrors).every((err) => !err);
    };

    const handleSave = async () => {
        if (!validateForm()) return;

        try {
            // Upload image first if selected
            if (selectedImage) {
                await dispatch(uploadProfilePicture(selectedImage)).unwrap();
            }

            // Update profile
            await dispatch(updateProfile({ firstName, lastName, mobile })).unwrap();

            Alert.alert('Success', 'Profile updated successfully', [
                { text: 'OK', onPress: () => navigation.goBack() },
            ]);
        } catch (error) {
            Alert.alert('Error', 'Failed to update profile. Please try again.');
        }
    };

    return (
        <ScrollView style={styles.container} contentContainerStyle={styles.content}>
            {/* Avatar */}
            <TouchableOpacity style={styles.avatarContainer} onPress={showImageOptions}>
                <Avatar
                    uri={selectedImage || customer?.profileImage}
                    name={`${firstName} ${lastName}`}
                    size={100}
                />
                <View style={styles.changePhotoButton}>
                    <View style={styles.cameraIcon}>
                        <View style={styles.cameraIconInner} />
                    </View>
                </View>
            </TouchableOpacity>

            {/* Form */}
            <Input
                label="First Name"
                value={firstName}
                onChangeText={setFirstName}
                placeholder="Enter first name"
                autoCapitalize="words"
                error={errors.firstName}
            />

            <Input
                label="Last Name"
                value={lastName}
                onChangeText={setLastName}
                placeholder="Enter last name"
                autoCapitalize="words"
                error={errors.lastName}
            />

            <Input
                label="Email"
                value={customer?.email || ''}
                placeholder="Email address"
                editable={false}
                containerStyle={styles.disabledInput}
            />

            <Input
                label="Mobile Number"
                value={mobile}
                onChangeText={setMobile}
                placeholder="Enter 10-digit mobile"
                keyboardType="phone-pad"
                maxLength={10}
                error={errors.mobile}
            />

            {/* Buttons */}
            <View style={styles.buttons}>
                <Button
                    text="Cancel"
                    onPress={() => navigation.goBack()}
                    variant="outline"
                    style={styles.button}
                />
                <Button
                    text="Save Changes"
                    onPress={handleSave}
                    loading={isLoading}
                    style={styles.button}
                />
            </View>
        </ScrollView>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.background,
    },
    content: {
        padding: spacing.xl,
    },
    avatarContainer: {
        alignSelf: 'center',
        marginBottom: spacing['2xl'],
    },
    changePhotoButton: {
        position: 'absolute',
        bottom: 0,
        right: 0,
        backgroundColor: colors.primary,
        width: 32,
        height: 32,
        borderRadius: 16,
        justifyContent: 'center',
        alignItems: 'center',
        borderWidth: 2,
        borderColor: colors.cardBackground,
    },
    cameraIcon: {
        width: 12,
        height: 10,
        backgroundColor: colors.textWhite,
        borderRadius: 2,
    },
    cameraIconInner: {
        width: 6,
        height: 6,
        backgroundColor: colors.primary,
        borderRadius: 3,
        alignSelf: 'center',
        marginTop: 2,
    },
    disabledInput: {
        opacity: 0.6,
    },
    buttons: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        marginTop: spacing.xl,
    },
    button: {
        flex: 1,
        marginHorizontal: spacing.xs,
    },
});

export default EditProfileScreen;
